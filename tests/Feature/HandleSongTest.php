<?php

use App\Infrastructure\Models\Song;
use App\Infrastructure\Models\User;
use Illuminate\Support\Str;

test('user can list songs filtering by status', function ($status, int $expectedCount) {
    $user = User::factory()->create();
    $this->actingAs($user);

    Song::factory()->count(5)->create(['status' => 'approved']);
    Song::factory()->count(3)->create(['status' => 'suggested']);
    Song::factory()->count(2)->create(['status' => 'rejected']);

    $response = $this->get(route(
        'dashboard.songs.list',
        ['filterBy' => 'status', 'filterValue' => $status]
    ));

    $response->assertStatus(200);
    $response->assertJsonCount($expectedCount);
})->with([
    'approved songs' => ['approved', 5],
    'suggested songs' => ['suggested', 3],
    'rejected songs' => ['rejected', 2],
    'no songs' => [null, 10],
]);

test('user can list songs filtering by approver', function ($approverId, int $expectedCount) {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var User $approver1 */
    $approver1 = User::factory()->create(['id' => 100]);
    /** @var User $approver2 */
    $approver2 = User::factory()->create(['id' => 200]);

    Song::factory()->count(4)->create([
        'status' => 'approved',
        'approved_by' => $approver1->id
    ]);
    Song::factory()->count(6)->create([
        'status' => 'approved',
        'approved_by' => $approver2->id
    ]);
    Song::factory()->count(5)->create(['approved_by' => null]);

    $response = $this->get(route(
        'dashboard.songs.list',
        ['filterBy' => 'approvedBy', 'filterValue' => $approverId]
    ));

    $response->assertStatus(200);
    $response->assertJsonCount($expectedCount);
})->with([
    'songs approved by approver 1' => [100, 4],
    'songs approved by approver 2' => [200, 6],
    'no songs' => [null, 10],
]);

test('user can list songs filtering by rejecter', function ($rejecterId, int $expectedCount) {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var User $rejecter1 */
    $rejecter1 = User::factory()->create(['id' => 300]);
    /** @var User $rejecter2 */
    $rejecter2 = User::factory()->create(['id' => 400]);

    Song::factory()->count(2)->create([
        'status' => 'rejected',
        'rejected_by' => $rejecter1->id
    ]);
    Song::factory()->count(7)->create([
        'status' => 'rejected',
        'rejected_by' => $rejecter2->id
    ]);
    Song::factory()->count(5)->create(['rejected_by' => null]);

    $response = $this->get(route(
        'dashboard.songs.list',
        ['filterBy' => 'rejectedBy', 'filterValue' => $rejecterId]
    ));

    $response->assertStatus(200);
    $response->assertJsonCount($expectedCount);
})->with([
    'songs rejected by rejecter 1' => [300, 2],
    'songs rejected by rejecter 2' => [400, 7],
    'no songs' => [null, 10]
]);

test('user can list different pages of different page sizes', function ($page, $perPage, $expectedCount) {
    $user = User::factory()->create();
    $this->actingAs($user);

    Song::factory()->count(25)->create();

    $response = $this->get(route(
        'dashboard.songs.list',
        ['page' => $page, 'perPage' => $perPage]
    ));

    $response->assertStatus(200);
    $response->assertJsonCount($expectedCount);
})->with([
    'first page, 10 per page' => [1, 10, 10],
    'second page, 10 per page' => [2, 10, 10],
    'third page, 10 per page' => [3, 10, 5],
    'first page, 15 per page' => [1, 15, 15],
    'second page, 15 per page' => [2, 15, 10],
    'first page, 30 per page' => [1, 30, 25]
]);

test('user cannot list songs using invalid pagination arguments', function ($page, $perPage) {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route(
        'dashboard.songs.list',
        ['page' => $page, 'perPage' => $perPage]
    ));

    $response->assertStatus(422);
})->with([
    'zero page' => [0, 10],
    'negative page' => [-1, 10],
    'zero per page' => [1, 0],
    'negative per page' => [1, -5],
    'too many per page' => [1, 100],
    'string page' => ['one', 10],
    'string per page' => [1, 'ten'],
]);

test('user can list songs sorting by valid properties on both directions', function ($property, $direction) {
    $user = User::factory()->create();
    $this->actingAs($user);

    Song::factory()->count(10)->create([
        'approved_at' => now()->subDays(rand(0, 10)),
        'rejected_at' => now()->subDays(rand(0, 10)),
        'created_at' => now()->subDays(rand(0, 10)),
        'views_count' => rand(0, 1000),
    ]);

    $response = $this->get(route(
        'dashboard.songs.list',
        ['sortBy' => $property, 'direction' => $direction]
    ));

    $response->assertStatus(200);
    $response->assertJsonCount(10);

    $songIds = Song::orderBy(Str::snake($property), $direction)->pluck('id')->toArray();
    $responseIds = array_map(fn($song) => data_get($song, 'id'), $response->json());

    $this->assertEquals($songIds, $responseIds);
})->with([
    'sort by approvedAt ascending' => ['approvedAt', 'asc'],
    'sort by approvedAt descending' => ['approvedAt', 'desc'],
    'sort by rejectedAt ascending' => ['rejectedAt', 'asc'],
    'sort by rejectedAt descending' => ['rejectedAt', 'desc'],
    'sort by createdAt ascending' => ['createdAt', 'asc'],
    'sort by createdAt descending' => ['createdAt', 'desc'],
    'sort by viewsCount ascending' => ['viewsCount', 'asc'],
    'sort by viewsCount descending' => ['viewsCount', 'desc'],
]);

test('user can retrieve a song by its ID', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create();

    $response = $this->get(route('dashboard.songs.show', ['id' => $song->id]));

    $response->assertStatus(200);
    $response->assertJsonStructure(['id', 'title', 'thumbnailUrl', 'viewsCount']);
    $this->assertEquals($song->id, $response->json('id'));
});

test('user can add a new song using a valid url', function ($link, $expectedId) {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('dashboard.songs.add'), ['link' => $link]);
    $response->assertStatus(201);

    $this->assertDatabaseHas('songs', ['id' => $expectedId]);
})->with([
    'standard' => ['https://www.youtube.com/watch?v=xI_-PyrkcsM', 'xI_-PyrkcsM'],
    'short' => ['https://youtu.be/mMI45ClswB4', 'mMI45ClswB4'],
    'embed' => ['https://www.youtube.com/embed/W9VbmlHIDtQ', 'W9VbmlHIDtQ'],
]);

test('user cannot add a new song using an invalid url', function ($link) {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('dashboard.songs.add'), ['link' => $link]);
    $response->assertStatus(422);

    $this->assertDatabaseCount('songs', 0);
})->with([
    'not a url' => ['not a url'],
    'empty string' => [''],
    'invalid youtube link' => ['https://www.example.com/watch?v=invalid'],
]);

test('user can update a song with valid data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create([
        'id' => 'xgGmZBzsHsQ',
        'title' => 'Old Title',
        'thumbnail_url' => 'http://old-thumbnail.url/image.jpg',
        'views_count' => 100,
    ]);

    $newData = [
        'title' => 'New Title',
        'thumbnail_url' => 'http://new-thumbnail.url/image.jpg',
        'views_count' => 500,
    ];

    $payload = [
        'title' => 'New Title',
        'thumbnailUrl' => 'http://new-thumbnail.url/image.jpg',
        'viewsCount' => 500,
    ];

    $response = $this->patch(
        route('dashboard.songs.update', ['id' => $song->id]),
        $payload
    );

    $response->assertStatus(200);

    $this->assertDatabaseHas('songs', array_merge(['id' => $song->id], $newData));
});

test('user cannot update a song with invalid data', function ($title, $thumbnailUrl, $viewsCount) {
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create();

    $payload = array_filter([
        'title' => $title,
        'thumbnailUrl' => $thumbnailUrl,
        'viewsCount' => $viewsCount,
    ]);

    $response = $this->patch(
        route('dashboard.songs.update', ['id' => $song->id]),
        $payload
    );

    $response->assertStatus(422);
})->with([
    'empty title' => ['', 'http://valid-thumbnail.url/image.jpg', 100],
    'too long title' => [Str::random(256), 'http://valid-thumbnail.url/image.jpg', 100],
    'invalid thumbnail URL' => ['Valid Title', 'not-a-url', 100],
    'negative views count' => ['Valid Title', 'http://valid-thumbnail.url/image.jpg', -10],
    'non-integer views count' => ['Valid Title', 'http://valid-thumbnail.url/image.jpg', 'a lot'],
]);

test('user can delete a song', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create();
    $response = $this->delete(route('dashboard.songs.delete', ['id' => $song->id]));
    $response->assertStatus(204);

    $song = Song::onlyTrashed()->find($song->id);
    $this->assertNotNull($song);
    $this->assertNotNull($song->deleted_at);
    $this->assertEquals($song->deleted_by, $user->id);
});

test('user can approve a suggested song', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create(['status' => 'suggested']);
    $response = $this->patch(route('dashboard.songs.approve', ['id' => $song->id]));
    $response->assertStatus(200);

    $this->assertDatabaseHas('songs', [
        'id' => $song->id,
        'status' => 'approved',
        'approved_by' => $user->id,
    ]);
});

test('user can reject a suggested song', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create(['status' => 'suggested']);
    $response = $this->patch(route('dashboard.songs.reject', ['id' => $song->id]));
    $response->assertStatus(200);

    $this->assertDatabaseHas('songs', [
        'id' => $song->id,
        'status' => 'rejected',
        'rejected_by' => $user->id,
    ]);
});

test('user cannot approve an already approved or rejected song', function ($initialStatus) {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create(['status' => $initialStatus]);
    $response = $this->patch(route('dashboard.songs.approve', ['id' => $song->id]));
    $response->assertStatus(400);

    $this->assertDatabaseHas('songs', [
        'id' => $song->id,
        'status' => $initialStatus,
        'approved_by' => null,
    ]);
})->with([
    'already approved' => ['approved'],
    'already rejected' => ['rejected'],
]);

test('user cannot reject an already approved or rejected song', function ($initialStatus) {
    /** @var User $user */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @var Song $song */
    $song = Song::factory()->create(['status' => $initialStatus]);
    $response = $this->patch(route('dashboard.songs.reject', ['id' => $song->id]));
    $response->assertStatus(400);

    $this->assertDatabaseHas('songs', [
        'id' => $song->id,
        'status' => $initialStatus,
        'rejected_by' => null,
    ]);
})->with([
    'already approved' => ['approved'],
    'already rejected' => ['rejected'],
]);

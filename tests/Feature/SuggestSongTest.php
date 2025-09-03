<?php

use App\Domain\Enum\SongStatus;
use App\Infrastructure\Models\Song;
use App\Infrastructure\Models\User;

test('providing a valid url results in a suggestion', function (string $url, string $id) {
    $response = $this->post(
        route('songs.suggest'),
        ['link' => $url]
    );

    $response->assertStatus(200);
    $response->assertJsonStructure();

    $this->assertDatabaseHas('songs', ['id' => $id]);
})->with([
    ['https://www.youtube.com/watch?v=xI_-PyrkcsM', 'xI_-PyrkcsM'],
    ['https://youtu.be/mMI45ClswB4', 'mMI45ClswB4'],
    ['https://www.youtube.com/embed/W9VbmlHIDtQ', 'W9VbmlHIDtQ'],
]);

test('providing an invalid url results in a 422', function (string $url) {
    $response = $this->post(
        route('songs.suggest'),
        ['link' => $url]
    );

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => ['link']
    ]);
})->with([
    'not a url' => ['not a url'],
    'empty string' => [''],
    'invalid youtube link' => ['https://www.example.com/watch?v=invalid'],
]);

test('can list a page of approved songs as a ranking', function () {
    $user = User::factory()->create();
    $songs = Song::factory()->count(30)->create([
        'status' => SongStatus::APPROVED->value,
        'approved_at' => now(),
        'approved_by' => 1,
    ]);

    $response = $this->get(
        route('songs.list', ['page' => 1, 'per_page' => 15])
    );

    $response->assertStatus(200);
    $response->assertJsonCount(15);
    $response->assertJsonStructure([
        '*' => ['title', 'thumbnailUrl', 'viewsCount']
    ]);

    $responseData = $response->json();
    $this->assertEquals(
        $songs->sortByDesc('views_count')->values()->take(15)->pluck('title')->toArray(),
        array_column($responseData, 'title')
    );
});

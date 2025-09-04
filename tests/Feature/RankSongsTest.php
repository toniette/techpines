<?php

use App\Domain\Enum\SongStatus;
use App\Infrastructure\Models\Song;
use App\Infrastructure\Models\User;

test('can list a page of approved songs as a ranking', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $songs = Song::factory()->count(30)->create([
        'status' => SongStatus::APPROVED->value,
        'approved_at' => now(),
        'approved_by' => $user->id,
    ]);

    $response = $this->get(
        route('songs.list', ['page' => 1, 'perPage' => 15])
    );

    $response->assertStatus(200);
    $response->assertJsonCount(15);
    $response->assertJsonStructure([
        '*' => ['title', 'thumbnailUrl', 'viewsCount'],
    ]);

    $responseData = $response->json();
    $this->assertEquals(
        $songs->sortByDesc('views_count')->values()->take(15)->pluck('title')->toArray(),
        array_column($responseData, 'title')
    );
});

test('requesting a page beyond the available songs returns an empty array', function (int $page, int $perPage) {
    /** @var User $user */
    $user = User::factory()->create();
    Song::factory()->count(10)->create([
        'status' => SongStatus::APPROVED->value,
        'approved_at' => now(),
        'approved_by' => $user->id,
    ]);

    $response = $this->get(
        route('songs.list', ['page' => $page, 'perPage' => $perPage])
    );

    $response->assertStatus(200);
    $response->assertExactJson([]);
})->with([
    'page 2 with 15 per page' => [2, 15],
    'page 3 with 5 per page' => [3, 5],
    'page 5 with 3 per page' => [5, 3],
]);

test('requesting a page with invalid page or perPage values returns a 422', function ($page, $perPage) {
    $response = $this->get(
        route('songs.list', ['page' => $page, 'perPage' => $perPage])
    );

    $response->assertStatus(422);
})->with([
    'zero page' => [0, 10],
    'negative page' => [-1, 10],
    'zero per page' => [1, 0],
    'negative per page' => [1, -5],
    'non-integer page' => ['one', 10],
    'non-integer per page' => [1, 'ten'],
    'exceeding max per page' => [1, 25],
]);

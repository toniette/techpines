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
    'standard' => ['https://www.youtube.com/watch?v=xI_-PyrkcsM', 'xI_-PyrkcsM'],
    'short' => ['https://youtu.be/mMI45ClswB4', 'mMI45ClswB4'],
    'embed' => ['https://www.youtube.com/embed/W9VbmlHIDtQ', 'W9VbmlHIDtQ'],
]);

test('providing an invalid url results in a 422', function (string $url) {
    $response = $this->post(
        route('songs.suggest'),
        ['link' => $url]
    );

    $response->assertStatus(422);
})->with([
    'not a url' => ['not a url'],
    'empty string' => [''],
    'invalid youtube link' => ['https://www.example.com/watch?v=invalid'],
]);

<?php

use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Hash;

test('unauthenticated users cannot access a protected route', function () {
    $response = $this->get(route('dashboard.songs.list'));
    $response->assertStatus(401);
});

test('user can get a token if using valid credentials', function (string $email, string $password) {
    User::factory()->create([
        'email' => $email,
        'password' => Hash::make($password),
    ]);

    $response = $this->post(route('login'), [
        'email' => $email,
        'password' => $password,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['access_token', 'token_type']);
})->with([
    ['admin@admin.com', '12345678']
]);

test('user cannot get a token if using invalid credentials', function (string $email, string $password) {
    User::factory()->create([
        'email' => 'admin@admin.com',
        'password' => Hash::make('validPassword123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => $email,
        'password' => $password,
    ]);
    $response->assertStatus(401);
    $this->assertEmpty($response->content());
})->with([
    'valid email, wrong password' => ['admin@admin.com', 'wrongPassword'],
    'wrong email, valid password' => ['wrong@email.com', 'validPassword123'],
    'wrong email, wrong password' => ['wrong@email.com', 'wrongPassword']
]);

test('authenticated users can access a protected route', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->get(route('dashboard.songs.list'));

    $response->assertStatus(200);
});

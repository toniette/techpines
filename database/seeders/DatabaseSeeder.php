<?php

namespace Database\Seeders;

use App\Domain\Enum\SongStatus;
use App\Infrastructure\Models\Song;
use App\Infrastructure\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
        ]);

        Song::factory(100)->create([
            'status' => SongStatus::SUGGESTED
        ]);

        Song::factory(100)->create([
            'approved_at' => now(),
            'approved_by' => $user->id,
            'status' => SongStatus::APPROVED
        ]);

        Song::factory(100)->create([
            'rejected_at' => now(),
            'rejected_by' => $user->id,
            'status' => SongStatus::REJECTED
        ]);
    }
}

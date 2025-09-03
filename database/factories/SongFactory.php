<?php

namespace Database\Factories;

use App\Domain\Enum\SongStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Models\Song>
 */
class SongFactory extends Factory
{

    public function definition(): array
    {
        return [
            'id' => Str::random(11),
            'title' => $this->faker->sentence(3),
            'thumbnail_url' => $this->faker->imageUrl(),
            'views_count' => $this->faker->numberBetween(0, 1000000),
            'status' => SongStatus::SUGGESTED->value,
            'created_at' => now(),
            'updated_at' => now(),
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => null,
            'rejected_by' => null,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }
}

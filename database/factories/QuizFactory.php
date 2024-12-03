<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            "title" => fake()->name(),
            "duration" => fake()->numberBetween(60, 120),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes): array => [
            "scheduled_at" => Carbon::now()->addMinutes(30),
            "locked_at" => Carbon::now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes): array => [
            "scheduled_at" => Carbon::now()->subMinutes(30),
            "locked_at" => Carbon::now(),
            "ranking_published_at" => null,
        ]);
    }
}

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
            "name" => fake()->name(),
            "duration" => fake()->numberBetween(3600, 7200),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes): array => [
            "scheduled_at" => Carbon::now(),
        ]);
    }
}

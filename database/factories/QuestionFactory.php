<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Question;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            "text" => fake()->realText(),
            "test_id" => Test::factory(),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes): array => [
            "test_id" => Test::factory()->locked(),
        ]);
    }
}

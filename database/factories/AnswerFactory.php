<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            "text" => fake()->realText(),
            "question_id" => QuestionFactory::factory(),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes): array => [
            "question_id" => QuestionFactory::factory()->locked(),
        ]);
    }
}

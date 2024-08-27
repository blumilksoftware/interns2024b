<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
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
            "quiz_id" => Quiz::factory(),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes): array => [
            "quiz_id" => Quiz::factory()->locked(),
            "correct_answer_id" => Answer::factory(),
        ]);
    }
}

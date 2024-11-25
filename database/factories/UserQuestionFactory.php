<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Question;
use App\Models\UserQuestion;
use App\Models\UserQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserQuestion>
 */
class UserQuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            "user_quiz_id" => UserQuiz::factory(),
            "question_id" => Question::factory(),
        ];
    }
}

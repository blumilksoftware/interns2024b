<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AnswerRecord;
use App\Models\Question;
use App\Models\QuizSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnswerRecord>
 */
class AnswerRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            "quiz_submission_id" => QuizSubmission::factory(),
            "question_id" => Question::factory(),
        ];
    }
}

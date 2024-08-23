<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\AnswerRecord;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create();
        $quiz = Quiz::factory()->create();

        $questions = Question::factory()->count(10)->create(["quiz_id" => $quiz->id]);

        foreach ($questions as $question) {
            Answer::factory()->count(4)->create(["question_id" => $question->id]);
        }

        AnswerRecord::factory()->create();
    }
}

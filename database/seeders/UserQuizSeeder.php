<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\AnswerRecord;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserQuizSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create();

        $quiz = Quiz::factory()
            ->has(
                Question::factory()
                    ->count(5)
                    ->has(
                        Answer::factory()->count(4),
                    ),
            )
            ->create();

        foreach ($quiz->questions as $question) {
            $answers = $question->answers;

            if ($answers->isNotEmpty()) {
                $correctAnswer = $answers->random();
                $question->correct_answer_id = $correctAnswer->id;
                $question->save();
            }
        }

        $quizSubmission = QuizSubmission::factory()
            ->for($quiz)
            ->for($user)
            ->create();

        foreach ($quiz->questions as $question) {
            $answers = $question->answers;
            $selectedAnswer = $answers->random();

            AnswerRecord::factory()
                ->for($quizSubmission)
                ->for($question)
                ->for($selectedAnswer)
                ->create();
        }
    }
}

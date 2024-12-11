<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class PublishedQuizSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = Quiz::factory()
            ->has(Question::factory()->count(5)->has(Answer::factory()->count(4)))
            ->published()
            ->create();

        self::selectRandomCorrectAnswer($quiz);
    }

    public static function selectRandomCorrectAnswer(Quiz $quiz): void
    {
        foreach ($quiz->questions as $question) {
            $answers = $question->answers;

            if ($answers->isNotEmpty()) {
                $correctAnswer = $answers->random();
                $question->correct_answer_id = $correctAnswer->id;
                $question->save();
            }
        }
    }
}

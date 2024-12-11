<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuestion;
use App\Models\UserQuiz;
use Illuminate\Database\Seeder;

class UserQuizSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->call([PublishedQuizSeeder::class]);
        $quiz = Quiz::query()->firstOrFail();

        self::createUserQuizForUser($quiz, $user1, null);
        self::createUserQuizForUser($quiz, $user2, null);
    }

    public static function createUserQuizForUser(Quiz $quiz, User $user, ?int $correctAnswersCount): UserQuiz
    {
        $userQuiz = UserQuiz::factory()
            ->for($quiz)
            ->for($user)
            ->create();

        $questions = $quiz->questions;

        if ($correctAnswersCount === null) {
            $correctAnswersCount = rand(0, $questions->count());
        }

        $shuffledQuestions = $questions->shuffle();

        $correctQuestions = $shuffledQuestions->take($correctAnswersCount);

        foreach ($questions as $question) {
            $answers = $question->answers;
            $isCorrect = $correctQuestions->contains($question);

            $selectedAnswer = $isCorrect
                ? $answers->where("id", $question->correct_answer_id)->first()
                : $answers->where("id", "!=", $question->correct_answer_id)->random();

            UserQuestion::factory()
                ->for($userQuiz)
                ->for($question)
                ->for($selectedAnswer)
                ->create();
        }

        return $userQuiz;
    }
}

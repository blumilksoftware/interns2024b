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
    public User $user1;
    public User $user2;
    public Quiz $quiz;
    public function run(): void
    {


        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        $this->quiz = Quiz::factory()
            ->has(
                Question::factory()
                    ->count(5)
                    ->has(
                        Answer::factory()->count(4),
                    ),
            )
            ->create();

        foreach ($this->quiz->questions as $question) {
            $answers = $question->answers;

            if ($answers->isNotEmpty()) {
                $correctAnswer = $answers->random();
                $question->correct_answer_id = $correctAnswer->id;
                $question->save();
            }
        }

        $this->createSubmissionForUser($this->user1, 2);
        $this->createSubmissionForUser($this->user2, 3);
    }

    private function createSubmissionForUser(User $user, ?int $correctAnswersCount): void
    {
        $quizSubmission = QuizSubmission::factory()
            ->for($this->quiz)
            ->for($user)
            ->create();

        $questions = $this->quiz->questions;

        if ($correctAnswersCount === null) {

            $correctAnswersCount = rand(0, $questions->count());
        }

        $shuffledQuestions = $questions->shuffle();

        $correctQuestions = $shuffledQuestions->take($correctAnswersCount);

        foreach ($questions as $question) {
            $answers = $question->answers;
            $isCorrect = $correctQuestions->contains($question);

            $selectedAnswer = $isCorrect
                ? $answers->where('id', $question->correct_answer_id)->first()
                : $answers->where('id', '!=', $question->correct_answer_id)->random();

            AnswerRecord::factory()
                ->for($quizSubmission)
                ->for($question)
                ->for($selectedAnswer)
                ->create();
        }
    }
}

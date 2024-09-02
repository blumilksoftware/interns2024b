<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\QuizSubmission;
use App\Models\User;
use Database\Seeders\UserQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserQuizSeeder::class);
        $this->user = User::first();
    }

    public function testUserHasPointsInAnsweredQuiz(): void
    {
        $quizSubmission = QuizSubmission::where("user_id", $this->user->id)->first();

        $quizSubmission->refresh();

        $expectedPoints = $quizSubmission->quiz->questions->filter(fn($question) => $question->answers->contains(fn($answer) => $answer->isCorrect))->count();

        $this->assertGreaterThanOrEqual(0, $quizSubmission->points);
        $this->assertLessThanOrEqual($quizSubmission->maxPoints, $quizSubmission->points);
    }
}

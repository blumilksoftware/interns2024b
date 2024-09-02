<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\AnswerRecord;
use App\Models\Question;
use App\Models\Quiz;
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

        $seeder = new UserQuizSeeder();
        $seeder->run();

        $this->user1 = $seeder->user1;
        $this->user2 = $seeder->user2;
        $this->quiz = $seeder->quiz;
    }

    public function testUserHasPointsInAnsweredQuiz(): void
    {
        $quizSubmission = QuizSubmission::where("user_id", $this->user1->id)->first();

        $quizSubmission->refresh();

        $this->assertGreaterThanOrEqual(0, $quizSubmission->points);
        $this->assertLessThanOrEqual($quizSubmission->maxPoints, $quizSubmission->points);
    }

    public function testUserPointsAreCalculatedCorrectly(): void
    {

        $quizSubmission1 = QuizSubmission::where('user_id', $this->user1->id)
            ->where('quiz_id', $this->quiz->id)
            ->first();

        $quizSubmission2 = QuizSubmission::where('user_id', $this->user2->id)
            ->where('quiz_id', $this->quiz->id)
            ->first();

        $quizSubmission1->refresh();
        $quizSubmission2->refresh();

        $user1Score = $quizSubmission1->points;
        $user2Score = $quizSubmission2->points;


        $this->assertEquals(2, $user1Score);
        $this->assertEquals(3, $user2Score);
    }
}

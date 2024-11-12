<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\UserQuiz;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserQuizTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanViewSingleUserQuiz(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);

        foreach ($questions as $question) {
            $answers = Answer::factory()->count(4)->create(["question_id" => $question->id]);
            $question->correctAnswer()->associate($answers[2]);
            $question->save();
        }

        $userQuiz = $quiz->createUserQuiz($this->user);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 2);
        $this->assertDatabaseCount("answers", 8);
        $this->assertDatabaseCount("user_quizzes", 1);
        $this->assertDatabaseCount("user_questions", 2);

        $this->actingAs($this->user)
            ->get("/quizzes/{$userQuiz->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/Quiz")
                    ->where("userQuiz.title", $quiz->title)
                    ->count("userQuiz.questions", 2)
                    ->count("userQuiz.questions.0.answers", 4)
                    ->missing("userQuiz.questions.0.answers.2.correct")
                    ->count("userQuiz.questions.1.answers", 4)
                    ->missing("userQuiz.questions.1.answers.2.correct"),
            );
    }

    public function testUserCannotViewUserQuizThatIsNotHis(): void
    {
        $userQuiz = UserQuiz::factory()->create();

        $this->actingAs($this->user)
            ->get("/quizzes/{$userQuiz->id}")
            ->assertStatus(403);
    }

    public function testUserCanSeeUserQuizResult(): void
    {
        $quiz = Quiz::factory()->create();
        $quiz->ranking_published_at = Carbon::now();
        $quiz->save();

        Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);
        $userQuiz = $quiz->createUserQuiz($this->user);
        $userQuiz->closed_at = Carbon::now();
        $userQuiz->save();

        $this->actingAs($this->user)
            ->get("/quizzes/{$userQuiz->id}/result")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/QuizResult")
                    ->where("userQuiz.title", $quiz->title)
                    ->count("userQuiz.questions", 2)
                    ->where("hasRanking", true),
            );
    }

    public function testUserCannotSeeUserQuizResultThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->get("/quizzes/0/result")
            ->assertStatus(404);
    }

    public function testUserCannotSeeUserQuizResultThatIsNotHis(): void
    {
        $userQuiz = UserQuiz::factory()->closed()->create();

        $this->actingAs($this->user)
            ->get("/quizzes/{$userQuiz->id}/result")
            ->assertStatus(403);
    }

    public function testUserCannotSeeUserQuizResultThatIsNotClosed(): void
    {
        $userQuiz = UserQuiz::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->get("/quizzes/{$userQuiz->id}/result")
            ->assertStatus(403);
    }

    public function testUserCanCloseUserQuiz(): void
    {
        $userQuiz = UserQuiz::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->post("/quizzes/{$userQuiz->id}/close")
            ->assertRedirect("/quizzes/{$userQuiz->id}/result")
            ->assertSessionHas(["status" => "Test został oddany."]);
    }

    public function testUserCannotCloseUserQuizThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->post("/quizzes/0/close")
            ->assertStatus(404);
    }

    public function testUserCannotCloseUserQuizThatIsNotHis(): void
    {
        $userQuiz = UserQuiz::factory()->create();

        $this->actingAs($this->user)
            ->post("/quizzes/{$userQuiz->id}/close")
            ->assertStatus(403);
    }

    public function testUserCannotCloseClosedUserQuiz(): void
    {
        $userQuiz = UserQuiz::factory()->closed()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->post("/quizzes/{$userQuiz->id}/close")
            ->assertStatus(403);
    }
}

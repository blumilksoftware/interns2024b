<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class QuizSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanViewSingleSubmission(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);

        foreach ($questions as $question) {
            $answers = Answer::factory()->count(4)->create(["question_id" => $question->id]);
            $question->correctAnswer()->associate($answers[2]);
            $question->save();
        }

        $submission = $quiz->createSubmission($this->user);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 2);
        $this->assertDatabaseCount("answers", 8);
        $this->assertDatabaseCount("quiz_submissions", 1);
        $this->assertDatabaseCount("answer_records", 2);

        $this->actingAs($this->user)
            ->get("/submissions/{$submission->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/Quiz")
                    ->where("submission.name", $quiz->name)
                    ->count("submission.answers", 2)
                    ->count("submission.answers.0.answers", 4)
                    ->missing("submission.answers.0.answers.2.correct")
                    ->count("submission.answers.1.answers", 4)
                    ->missing("submission.answers.1.answers.2.correct"),
            );
    }

    public function testUserCannotViewSubmissionThatIsNotHis(): void
    {
        $submission = QuizSubmission::factory()->create();

        $this->actingAs($this->user)
            ->get("/submissions/{$submission->id}")
            ->assertStatus(403);
    }

    public function testUserCanCloseSubmission(): void
    {
        $submission = QuizSubmission::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->post("/submissions/{$submission->id}/close")
            ->assertRedirect("/submissions/{$submission->id}/result")
            ->assertSessionHas(["status" => "Test został oddany."]);
    }

    public function testUserCannotCloseSubmissionThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->post("/submissions/0/close")
            ->assertStatus(404);
    }

    public function testUserCannotCloseSubmissionThatIsNotHis(): void
    {
        $submission = QuizSubmission::factory()->create();

        $this->actingAs($this->user)
            ->post("/submissions/{$submission->id}/close")
            ->assertStatus(403);
    }

    public function testUserCannotCloseClosedSubmission(): void
    {
        $submission = QuizSubmission::factory()->closed()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->post("/submissions/{$submission->id}/close")
            ->assertStatus(403);
    }

    public function testUserCanSeeSubmissionResult(): void
    {
        $quiz = Quiz::factory()->create();
        $quiz->ranking_published_at = Carbon::now();
        $quiz->save();

        Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);
        $submission = $quiz->createSubmission($this->user);
        $submission->closed_at = Carbon::now();
        $submission->save();

        $this->actingAs($this->user)
            ->get("/submissions/{$submission->id}/result")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/QuizResult")
                    ->where("submission.name", $quiz->name)
                    ->count("submission.answers", 2)
                    ->where("hasRanking", true),
            );
    }

    public function testUserCannotSeeSubmissionResultThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->get("/submissions/0/result")
            ->assertStatus(404);
    }

    public function testUserCannotSeeSubmissionResultThatIsNotHis(): void
    {
        $submission = QuizSubmission::factory()->closed()->create();

        $this->actingAs($this->user)
            ->get("/submissions/{$submission->id}/result")
            ->assertStatus(403);
    }

    public function testUserCannotSeeSubmissionResultThatIsNotClosed(): void
    {
        $submission = QuizSubmission::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->get("/submissions/{$submission->id}/result")
            ->assertStatus(403);
    }
}

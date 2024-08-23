<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
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
        $this->user->assignRole("user");
    }

    public function testUserCanViewSingleSubmission(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);
        Answer::factory()->count(4)->create(["question_id" => $questions[0]->id]);
        Answer::factory()->count(4)->create(["question_id" => $questions[1]->id]);
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
                    ->component("Submission/Show")
                    ->where("submission.name", $quiz->name)
                    ->count("submission.answers", 2)
                    ->count("submission.answers.0.answers", 4)
                    ->count("submission.answers.1.answers", 4),
            );
    }

    public function testUserCannotViewSubmissionThatIsNotHis(): void
    {
        $submission = QuizSubmission::factory()->create();

        $this->actingAs($this->user)
            ->get("/submissions/{$submission->id}")
            ->assertStatus(403);
    }
}

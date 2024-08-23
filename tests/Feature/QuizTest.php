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

class QuizTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10));

        $this->admin = User::factory()->create();
        $this->admin->assignRole("admin");
        $this->user = User::factory()->create();
        $this->user->assignRole("user");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testAdminCanViewQuizzes(): void
    {
        $quizzes = Quiz::factory()->count(2)->create();
        Question::factory()->count(5)->create(["quiz_id" => $quizzes[0]->id]);
        Question::factory()->count(5)->create(["quiz_id" => $quizzes[1]->id]);

        $this->assertDatabaseCount("quizzes", 2);
        $this->assertDatabaseCount("questions", 10);

        $this->actingAs($this->admin)
            ->get("/admin/quizzes")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Quiz/Index")
                    ->has("quizzes", 2)
                    ->has("quizzes.0.questions", 5)
                    ->has("quizzes.1.questions", 5),
            );
    }

    public function testAdminCannotViewQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)->get("/admin/quizzes/1")
            ->assertStatus(404);
    }

    public function testAdminCanViewSingleQuiz(): void
    {
        $quiz = Quiz::factory()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$quiz->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Quiz/Show")
                    ->where("quiz.id", $quiz->id),
            );
    }

    public function testAdminCanViewLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$quiz->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Quiz/Show")
                    ->where("quiz.id", $quiz->id)
                    ->where("quiz.locked", true),
            );
    }

    public function testAdminCanCreateQuiz(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz", "scheduled_at" => "2024-02-10 11:40:00"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "name" => "Example quiz",
            "scheduled_at" => "2024-02-10 11:40:00",
        ]);
    }

    public function testAdminCanCreateQuizWithoutDate(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10));

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "name" => "Example quiz",
            "scheduled_at" => null,
        ]);
    }

    public function testAdminCanCreateMultipleQuizzes(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 1"]);
        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 2"]);
        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 2"]);
    }

    public function testAdminCannotCreateInvalidQuiz(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "scheduled_at" => "invalid format"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "scheduled_at" => "2022-01-01 01:01:01"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "duration" => -100])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "duration" => 0])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->assertDatabaseCount("quizzes", 0);
    }

    public function testAdminCanEditQuiz(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "New quiz", "scheduled_at" => "2024-03-10 12:15:00", "duration" => 7200])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", ["name" => "New quiz", "scheduled_at" => "2024-03-10 12:15:00", "duration" => 7200]);
    }

    public function testAdminCannotEditQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/1", ["name" => "New quiz"])
            ->assertStatus(404);
    }

    public function testAdminCannotMakeInvalidEdit(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "scheduled_at" => "invalid format"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "scheduled_at" => "2022-01-01 01:01:01"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "duration" => -100])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "duration" => 0])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->assertDatabaseHas("quizzes", ["name" => "Old quiz"]);
    }

    public function testAdminCannotEditLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["name" => "Old quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "New quiz"])
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["name" => "Old quiz"]);
    }

    public function testAdminCanDeleteQuiz(): void
    {
        $quiz = Quiz::factory()->create(["name" => "quiz"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        Answer::factory()->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("quizzes", ["name" => "quiz"]);
        $this->assertDatabaseCount("quizzes", 0);
        $this->assertDatabaseCount("questions", 0);
        $this->assertDatabaseCount("answers", 0);
    }

    public function testAdminCannotDeleteLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["name" => "quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["name" => "quiz"]);
    }

    public function testAdminCannotDeleteQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/1")
            ->assertStatus(404);
    }

    public function testAdminCanCopyQuiz(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);

        Answer::factory()->count(10)->create(["question_id" => $questions[0]->id]);
        Answer::factory()->count(10)->create(["question_id" => $questions[1]->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 2);
        $this->assertDatabaseCount("answers", 20);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/clone")
            ->assertRedirect("/");

        $this->assertDatabaseCount("quizzes", 2);
        $this->assertDatabaseCount("questions", 4);
        $this->assertDatabaseCount("answers", 40);
    }

    public function testAdminCanCopyLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/clone")
            ->assertRedirect("/");

        $this->assertDatabaseCount("quizzes", 2);
    }

    public function testAdminCannotCopyQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/2/clone")
            ->assertStatus(404);
    }

    public function testUserCanStartQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();
        $user = User::factory()->create();
        $user->assignRole("user");

        $response = $this->actingAs($user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start");

        $submission = QuizSubmission::query()->where([
            "user_id" => $user->id,
            "quiz_id" => $quiz->id,
        ])->firstOrFail();

        $response->assertRedirect("/submissions/$submission->id/");
    }

    public function testUserCannotStartAlreadyStartedQuiz(): void
    {
        $submission = QuizSubmission::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$submission->quiz->id}/start")
            ->assertRedirect("/submissions/{$submission->id}/");

        $this->assertDatabaseCount("quiz_submissions", 1);
    }

    public function testUserCannotStartUnlockedQuiz(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start")
            ->assertStatus(403);

        $this->assertDatabaseCount("quiz_submissions", 0);
    }

    public function testUserCannotAccessToCrud(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->get(route("admin.quizzes.index"))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post(route("admin.quizzes.store"))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->get(route("admin.quizzes.show", $quiz->id))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->patch(route("admin.quizzes.update", $quiz->id))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post(route("admin.quizzes.clone", $quiz->id))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->delete(route("admin.quizzes.destroy", $quiz->id))
            ->assertStatus(403);
    }
}

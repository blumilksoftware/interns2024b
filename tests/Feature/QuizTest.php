<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
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

        $this->user = User::factory()->create();
    }

    public function testUserCanViewQuizzes(): void
    {
        $quizzes = Quiz::factory()->count(2)->create();
        Question::factory()->count(5)->create(["quiz_id" => $quizzes[0]->id]);
        Question::factory()->count(5)->create(["quiz_id" => $quizzes[1]->id]);

        $this->assertDatabaseCount("quizzes", 2);
        $this->assertDatabaseCount("questions", 10);

        $this->actingAs($this->user)
            ->get("/quizzes")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Quiz/Index")
                    ->has("quizzes", 2)
                    ->has("quizzes.0.questions", 5)
                    ->has("quizzes.1.questions", 5),
            );
    }

    public function testUserCannotViewQuizThatNotExisted(): void
    {
        $this->actingAs($this->user)->get("/quizzes/1")
            ->assertStatus(404);
    }

    public function testUserCanViewSingleQuiz(): void
    {
        $quiz = Quiz::factory()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->user)
            ->get("/quizzes/{$quiz->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Quiz/Show")
                    ->where("quiz.id", $quiz->id),
            );
    }

    public function testUserCanViewLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->user)
            ->get("/quizzes/{$quiz->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Quiz/Show")
                    ->where("quiz.id", $quiz->id)
                    ->where("quiz.locked", true),
            );
    }

    public function testUserCanCreateQuiz(): void
    {
        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes", ["name" => "Example quiz"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "name" => "Example quiz",
        ]);
    }

    public function testUserCanCreateMultipleQuizzes(): void
    {
        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes", ["name" => "Example quiz 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/quizzes", ["name" => "Example quiz 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/quizzes", ["name" => "Example quiz 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 1"]);
        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 2"]);
        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 2"]);
    }

    public function testUserCannotCreateInvalidQuiz(): void
    {
        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->post("/quizzes", ["name" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->assertDatabaseCount("quizzes", 0);
    }

    public function testUserCanEditQuiz(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz"]);

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/quizzes/{$quiz->id}", ["name" => "New quiz"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", ["name" => "New quiz"]);
    }

    public function testUserCannotEditQuizThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->from("/")
            ->patch("/quizzes/1", ["name" => "New quiz"])
            ->assertStatus(404);
    }

    public function testUserCannotMakeInvalidEdit(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz"]);

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/quizzes/{$quiz->id}", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->patch("/quizzes/{$quiz->id}", ["name" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->assertDatabaseHas("quizzes", ["name" => "Old quiz"]);
    }

    public function testUserCannotEditLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["name" => "Old quiz"]);

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/quizzes/{$quiz->id}", ["name" => "New quiz"])
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["name" => "Old quiz"]);
    }

    public function testUserCanDeleteQuiz(): void
    {
        $quiz = Quiz::factory()->create(["name" => "quiz"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        Answer::factory()->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($this->user)
            ->from("/")
            ->delete("/quizzes/{$quiz->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("quizzes", ["name" => "quiz"]);
        $this->assertDatabaseCount("quizzes", 0);
        $this->assertDatabaseCount("questions", 0);
        $this->assertDatabaseCount("answers", 0);
    }

    public function testUserCannotDeleteLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["name" => "quiz"]);

        $this->actingAs($this->user)
            ->from("/")
            ->delete("/quizzes/{$quiz->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["name" => "quiz"]);
    }

    public function testUserCannotDeleteQuestionThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->from("/")
            ->delete("/quizzes/1")
            ->assertStatus(404);
    }

    public function testUserCanCopyQuiz(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);

        Answer::factory()->count(10)->create(["question_id" => $questions[0]->id]);
        Answer::factory()->count(10)->create(["question_id" => $questions[1]->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 2);
        $this->assertDatabaseCount("answers", 20);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/clone")
            ->assertRedirect("/");

        $this->assertDatabaseCount("quizzes", 2);
        $this->assertDatabaseCount("questions", 4);
        $this->assertDatabaseCount("answers", 40);
    }

    public function testUserCanCopyLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/clone")
            ->assertRedirect("/");

        $this->assertDatabaseCount("quizzes", 2);
    }

    public function testUserCannotCopyQuizThatNotExisted(): void
    {
        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/2/clone")
            ->assertStatus(404);
    }
}
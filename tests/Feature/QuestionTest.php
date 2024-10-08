<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    public function testUserCanCreateQuestion(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/questions", ["text" => "Example question"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", [
            "text" => "Example question",
            "quiz_id" => $quiz->id,
        ]);
    }

    public function testAdminCanCreateMultipleQuestions(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/questions", ["text" => "Example question 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes/{$quiz->id}/questions", ["text" => "Example question 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes/{$quiz->id}/questions", ["text" => "Example question 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", ["text" => "Example question 1"]);
        $this->assertDatabaseHas("questions", ["text" => "Example question 2"]);
        $this->assertDatabaseHas("questions", ["text" => "Example question 2"]);
    }

    public function testAdminCannotCreateQuestionToQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/1/questions", ["text" => "Example question"])
            ->assertStatus(404);

        $this->assertDatabaseMissing("questions", [
            "text" => "Example question",
        ]);
    }

    public function testAdminCannotCreateQuestionToQuizThatIsLocked(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/quizzes/{$quiz->id}/questions", ["text" => "Example question 1"])
            ->assertStatus(403);

        $this->assertDatabaseMissing("questions", [
            "text" => "Example question",
        ]);
    }

    public function testAdminCannotCreateInvalidQuestion(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/questions", [])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->from("/")
            ->post("/admin/quizzes/{$quiz->id}/questions", ["text" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseCount("questions", 0);
    }

    public function testAdminCanEditQuestion(): void
    {
        $question = Question::factory()->create(["text" => "Old questions"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/questions/{$question->id}", ["text" => "New question"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", ["text" => "New question"]);
    }

    public function testAdminCannotEditQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/questions/1", ["text" => "New question"])
            ->assertStatus(404);
    }

    public function testAdminCannotMakeInvalidEdit(): void
    {
        $question = Question::factory()->create(["text" => "Old questions"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/questions/{$question->id}", [])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->from("/")
            ->patch("/admin/questions/{$question->id}", ["text" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseHas("questions", ["text" => "Old questions"]);
    }

    public function testAdminCannotEditLockedQuestion(): void
    {
        $question = Question::factory()->locked()->create(["text" => "Old question"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/questions/{$question->id}", ["text" => "New question"])
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["text" => "Old question"]);
    }

    public function testAdminCanDeleteQuestion(): void
    {
        $question = Question::factory()->create(["text" => "question"]);
        Answer::factory()->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/questions/{$question->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("questions", ["text" => "question"]);
        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 0);
        $this->assertDatabaseCount("answers", 0);
    }

    public function testAdminCannotDeleteLockedQuestion(): void
    {
        $question = Question::factory()->locked()->create(["text" => "question"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/questions/{$question->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["text" => "question"]);
    }

    public function testAdminCannotDeleteQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/questions/1")
            ->assertStatus(404);
    }

    public function testAdminCanCopyQuestion(): void
    {
        $quizA = Quiz::factory()->create();
        $quizB = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);
        Answer::factory()->count(10)->create(["question_id" => $question->id]);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);
        $this->assertDatabaseCount("answers", 10);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/clone/{$quizB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizB->id]);
        $this->assertDatabaseCount("answers", 20);
    }

    public function testAdminCanCopyLockedQuestion(): void
    {
        $quizA = Quiz::factory()->locked()->create();
        $quizB = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/clone/{$quizB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizB->id]);
    }

    public function testAdminCannotCopyAnswerToLockedQuestion(): void
    {
        $quizA = Quiz::factory()->create();
        $quizB = Quiz::factory()->locked()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/clone/{$quizB->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);
    }

    public function testAdminCanCopyQuestionWithCorrectAnswer(): void
    {
        $quizA = Quiz::factory()->create();
        $quizB = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);
        $answer = Answer::factory()->create(["text" => "correct", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answer);
        $question->save();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/clone/{$quizB->id}")
            ->assertRedirect("/quizzes");

        $this->assertNotNull($quizA->questions[0]->correctAnswer);
        $this->assertNotNull($quizB->questions[0]->correctAnswer);
        $this->assertNotEquals($quizA->questions[0]->correctAnswer->id, $quizB->questions[0]->correctAnswer->id);
    }

    public function testAdminCannotCopyQuestionThatNotExisted(): void
    {
        $quiz = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/2/clone/{$quiz->id}")
            ->assertStatus(404);
    }

    public function testAdminCannotCopyAnswerToQuestionThatNotExisted(): void
    {
        $question = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/clone/2")
            ->assertStatus(404);
    }

    public function testUserCannotAccessToCrud(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("admin/quizzes/$quiz->id/questions", ["text" => "New question"])
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->patch("admin/questions/$question->id", ["text" => "Updated question"])
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post("admin/questions/$question->id/clone/$quiz->id")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->delete("admin/questions/$question->id")
            ->assertStatus(403);
    }
}

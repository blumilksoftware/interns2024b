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

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    public function testAdminCanViewQuizQuestions(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        Answer::factory()->count(10)->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 10);

        $this->actingAs($this->admin)
            ->get(route("admin.questions.index", $quiz->id))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Question/Index")
                    ->has("questions", 1)
                    ->has("questions.0.answers", 10),
            );
    }

    public function testAdminCannotViewQuestionsOfQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)->get(route("admin.questions.index", 1))
            ->assertStatus(404);
    }

    public function testAdminCanViewSingleQuestion(): void
    {
        $question = Question::factory()->create();

        $this->assertDatabaseCount("questions", 1);

        $this->actingAs($this->admin)
            ->get(route("admin.questions.show", $question->id))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Question/Show")
                    ->where("question.id", $question->id),
            );
    }

    public function testAdminCanViewLockedQuestion(): void
    {
        $question = Question::factory()->locked()->create();

        $this->assertDatabaseCount("questions", 1);

        $this->actingAs($this->admin)
            ->get(route("admin.questions.show", $question->id))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Question/Show")
                    ->where("question.id", $question->id)
                    ->where("question.locked", true),
            );
    }

    public function testAdminCannotViewQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)->get(route("admin.questions.show", 1))
            ->assertStatus(404);
    }

    public function testAdminCanCreateQuestion(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post(route("admin.questions.store", $quiz->id), ["text" => "Example question"])
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
            ->post(route("admin.questions.store", $quiz->id), ["text" => "Example question 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post(route("admin.questions.store", $quiz->id), ["text" => "Example question 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post(route("admin.questions.store", $quiz->id), ["text" => "Example question 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", ["text" => "Example question 1"]);
        $this->assertDatabaseHas("questions", ["text" => "Example question 2"]);
        $this->assertDatabaseHas("questions", ["text" => "Example question 2"]);
    }

    public function testAdminCannotCreateQuestionToQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post(route("admin.questions.store", 1), ["text" => "Example question"])
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
            ->post(route("admin.questions.store", $quiz->id), ["text" => "Example question 1"])
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
            ->post(route("admin.questions.store", $quiz->id), [])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->from("/")
            ->post(route("admin.questions.store", $quiz->id), ["text" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseCount("questions", 0);
    }

    public function testAdminCanEditQuestion(): void
    {
        $question = Question::factory()->create(["text" => "Old questions"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch(route("admin.questions.update", $question->id), ["text" => "New question"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", ["text" => "New question"]);
    }

    public function testAdminCannotEditQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->patch(route("admin.questions.update", 1), ["text" => "New question"])
            ->assertStatus(404);
    }

    public function testAdminCannotMakeInvalidEdit(): void
    {
        $question = Question::factory()->create(["text" => "Old questions"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch(route("admin.questions.update", $question->id), [])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->from("/")
            ->patch(route("admin.questions.update", $question->id), ["text" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseHas("questions", ["text" => "Old questions"]);
    }

    public function testAdminCannotEditLockedQuestion(): void
    {
        $question = Question::factory()->locked()->create(["text" => "Old question"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch(route("admin.questions.update", $question->id), ["text" => "New question"])
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
            ->delete(route("admin.questions.destroy", $question->id))
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
            ->delete(route("admin.questions.destroy", $question->id))
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
            ->post(route("admin.questions.clone", ["question" => $question->id, "quiz" => $quizB->id]))
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
            ->post(route("admin.questions.clone", ["question" => $question->id, "quiz" => $quizB->id]))
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
            ->post(route("admin.questions.clone", ["question" => $question->id, "quiz" => $quizB->id]))
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
            ->post(route("admin.questions.clone", ["question" => $question->id, "quiz" => $quizB->id]))
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
            ->post(route("admin.questions.clone", ["question" => 2, "quiz" => $quiz->id]))
            ->assertStatus(404);
    }

    public function testAdminCannotCopyAnswerToQuestionThatNotExisted(): void
    {
        $question = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post(route("admin.questions.clone", ["question" => $question->id, "quiz" => 2]))
            ->assertStatus(404);
    }

    public function testUserCannotAccessToCrud(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);

        $this->actingAs($this->user)
            ->from("/")
            ->get(route("admin.questions.index", $quiz->id))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post(route("admin.questions.store", $quiz->id), ["text" => "New question"])
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->get(route("admin.questions.show", $question->id))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->patch(route("admin.questions.update", $question->id), ["text" => "Updated question"])
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post(route("admin.questions.clone", ["question" => $question->id, "quiz" => $quiz->id]))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->delete(route("admin.questions.destroy", $question->id))
            ->assertStatus(403);
    }
}

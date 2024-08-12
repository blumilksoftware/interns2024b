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

    public function testUserCanViewQuizQuestions(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        Answer::factory()->count(10)->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 10);

        $this->actingAs($user)
            ->get("/quizzes/{$quiz->id}/questions")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Question/Index")
                    ->has("questions", 1)
                    ->has("questions.0.answers", 10),
            );
    }

    public function testUserCannotViewQuestionsOfQuizThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/quizzes/1/questions")
            ->assertStatus(404);
    }

    public function testUserCanViewSingleQuestion(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();

        $this->assertDatabaseCount("questions", 1);

        $this->actingAs($user)
            ->get("/questions/{$question->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Question/Show")
                    ->where("question.id", $question->id),
            );
    }

    public function testUserCanViewLockedQuestion(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->locked()->create();

        $this->assertDatabaseCount("questions", 1);

        $this->actingAs($user)
            ->get("/questions/{$question->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Question/Show")
                    ->where("question.id", $question->id)
                    ->where("question.locked", true),
            );
    }

    public function testUserCannotViewQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/questions/1")
            ->assertStatus(404);
    }

    public function testUserCanCreateQuestion(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();

        $this->actingAs($user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/questions", ["text" => "Example question"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", [
            "text" => "Example question",
            "quiz_id" => $quiz->id,
        ]);
    }

    public function testUserCanCreateMultipleQuestions(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();

        $this->actingAs($user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/questions", ["text" => "Example question 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/quizzes/{$quiz->id}/questions", ["text" => "Example question 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/quizzes/{$quiz->id}/questions", ["text" => "Example question 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", ["text" => "Example question 1"]);
        $this->assertDatabaseHas("questions", ["text" => "Example question 2"]);
        $this->assertDatabaseHas("questions", ["text" => "Example question 2"]);
    }

    public function testUserCannotCreateQuestionToQuizThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/")
            ->post("/quizzes/1/questions", ["text" => "Example question"])
            ->assertStatus(404);

        $this->assertDatabaseMissing("questions", [
            "text" => "Example question",
        ]);
    }

    public function testUserCannotCreateQuestionToQuizThatIsLocked(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->locked()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/quizzes/{$quiz->id}/questions", ["text" => "Example question 1"])
            ->assertStatus(403);

        $this->assertDatabaseMissing("questions", [
            "text" => "Example question",
        ]);
    }

    public function testUserCannotCreateInvalidQuestion(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();

        $this->actingAs($user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/questions", [])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->from("/")
            ->post("/quizzes/{$quiz->id}/questions", ["text" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseCount("questions", 0);
    }

    public function testUserCanEditQuestion(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(["text" => "Old questions"]);

        $this->actingAs($user)
            ->from("/")
            ->patch("/questions/{$question->id}", ["text" => "New question"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("questions", ["text" => "New question"]);
    }

    public function testUserCannotEditQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/")
            ->patch("/questions/1", ["text" => "New question"])
            ->assertStatus(404);
    }

    public function testUserCannotMakeInvalidEdit(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(["text" => "Old questions"]);

        $this->actingAs($user)
            ->from("/")
            ->patch("/questions/{$question->id}", [])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->from("/")
            ->patch("/questions/{$question->id}", ["text" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseHas("questions", ["text" => "Old questions"]);
    }

    public function testUserCannotEditLockedQuestion(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->locked()->create(["text" => "Old question"]);

        $this->actingAs($user)
            ->from("/")
            ->patch("/questions/{$question->id}", ["text" => "New question"])
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["text" => "Old question"]);
    }

    public function testUserCanDeleteQuestion(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(["text" => "question"]);
        Answer::factory()->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($user)
            ->from("/")
            ->delete("/questions/{$question->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("questions", ["text" => "question"]);
        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 0);
        $this->assertDatabaseCount("answers", 0);
    }

    public function testUserCannotDeleteLockedQuestion(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->locked()->create(["text" => "question"]);

        $this->actingAs($user)
            ->from("/")
            ->delete("/questions/{$question->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["text" => "question"]);
    }

    public function testUserCannotDeleteQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/")
            ->delete("/questions/1")
            ->assertStatus(404);
    }

    public function testUserCanCopyQuestion(): void
    {
        $user = User::factory()->create();
        $quizA = Quiz::factory()->create();
        $quizB = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);
        Answer::factory()->count(10)->create(["question_id" => $question->id]);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);
        $this->assertDatabaseCount("answers", 10);

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/clone/{$quizB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizB->id]);
        $this->assertDatabaseCount("answers", 20);
    }

    public function testUserCanCopyLockedQuestion(): void
    {
        $user = User::factory()->create();
        $quizA = Quiz::factory()->locked()->create();
        $quizB = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/clone/{$quizB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizB->id]);
    }

    public function testUserCannotCopyAnswerToLockedQuestion(): void
    {
        $user = User::factory()->create();
        $quizA = Quiz::factory()->create();
        $quizB = Quiz::factory()->locked()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/clone/{$quizB->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["quiz_id" => $quizA->id]);
    }

    public function testUserCanCopyQuestionWithCorrectAnswer(): void
    {
        $user = User::factory()->create();
        $quizA = Quiz::factory()->create();
        $quizB = Quiz::factory()->create();
        $question = Question::factory()->create(["quiz_id" => $quizA->id]);
        $answer = Answer::factory()->create(["text" => "correct", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answer);
        $question->save();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/clone/{$quizB->id}")
            ->assertRedirect("/quizzes");

        $this->assertNotNull($quizA->questions[0]->correctAnswer);
        $this->assertNotNull($quizB->questions[0]->correctAnswer);
        $this->assertNotEquals($quizA->questions[0]->correctAnswer->id, $quizB->questions[0]->correctAnswer->id);
    }

    public function testUserCannotCopyQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();
        $quiz = Question::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/2/clone/{$quiz->id}")
            ->assertStatus(404);
    }

    public function testUserCannotCopyAnswerToQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/clone/2")
            ->assertStatus(404);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function testAdminCanCreateAnswer(): void
    {
        $question = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", ["text" => "Example answer"])
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", [
            "text" => "Example answer",
            "question_id" => $question->id,
        ]);
    }

    public function testAdminCanCreateMultipleAnswers(): void
    {
        $question = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", ["text" => "Example answer 1"])
            ->assertRedirect("/quizzes");

        $this->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", ["text" => "Example answer 2"])
            ->assertRedirect("/quizzes");

        $this->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", ["text" => "Example answer 3"])
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["text" => "Example answer 1"]);
        $this->assertDatabaseHas("answers", ["text" => "Example answer 2"]);
        $this->assertDatabaseHas("answers", ["text" => "Example answer 2"]);
    }

    public function testAdminCannotCreateAnswerToQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/1/answers", ["text" => "Example answer"])
            ->assertStatus(404);

        $this->assertDatabaseMissing("answers", [
            "text" => "Example answer",
        ]);
    }

    public function testAdminCannotCreateAnswerToQuestionThatIsLocked(): void
    {
        $question = Question::factory()->locked()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", ["text" => "Example answer 1"])
            ->assertStatus(403);

        $this->assertDatabaseMissing("answers", [
            "text" => "Example answer",
        ]);
    }

    public function testAdminCannotCreateInvalidAnswer(): void
    {
        $question = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", [])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->from("/quizzes")
            ->post("/admin/questions/{$question->id}/answers", ["text" => false])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseCount("answers", 0);
    }

    public function testAdminCanEditAnswer(): void
    {
        $answer = Answer::factory()->create(["text" => "Old answer"]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->patch("/admin/answers/{$answer->id}", ["text" => "New answer"])
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["text" => "New answer"]);
    }

    public function testAdminCannotEditAnswerThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->patch("/admin/answers/1", ["text" => "New answer"])
            ->assertStatus(404);
    }

    public function testAdminCannotMakeInvalidEdit(): void
    {
        $answer = Answer::factory()->create(["text" => "Old answer"]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->patch("/admin/answers/{$answer->id}", [])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->from("/quizzes")
            ->patch("/admin/answers/{$answer->id}", ["text" => true])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseHas("answers", ["text" => "Old answer"]);
    }

    public function testAdminCannotEditLockedAnswer(): void
    {
        $answer = Answer::factory()->locked()->create(["text" => "Old answer"]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->patch("/admin/answers/{$answer->id}", ["text" => "New answer"])
            ->assertStatus(403);

        $this->assertDatabaseHas("answers", ["text" => "Old answer"]);
    }

    public function testAdminCanDeleteAnswer(): void
    {
        $answer = Answer::factory()->create(["text" => "answer"]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->delete("/admin/answers/{$answer->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseMissing("answers", ["text" => "answer"]);
    }

    public function testAdminCannotDeleteLockedAnswer(): void
    {
        $answer = Answer::factory()->locked()->create(["text" => "answer"]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->delete("/admin/answers/{$answer->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("answers", ["text" => "answer"]);
    }

    public function testAdminCannotDeleteAnswerThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->delete("/admin/answers/1")
            ->assertStatus(404);
    }

    public function testAdminCanMarkAnswerAsCorrect(): void
    {
        $answer = Answer::factory()->create(["text" => "answer"]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/correct")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answer->id]);
    }

    public function testAdminCanChangeCorrectAnswer(): void
    {
        $question = Question::factory()->create();
        $answerA = Answer::factory()->create(["text" => "answer A", "question_id" => $question->id]);
        $answerB = Answer::factory()->create(["text" => "answer B", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answerA);
        $question->save();

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answerA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answerB->id}/correct")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answerB->id]);
    }

    public function testAdminCanDeleteCorrectAnswer(): void
    {
        $question = Question::factory()->create();
        $answer = Answer::factory()->create(["text" => "answer", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answer);
        $question->save();

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answer->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->delete("/admin/answers/{$answer->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["correct_answer_id" => null]);
    }

    public function testAdminCannotChangeCorrectAnswerInLockedQuestion(): void
    {
        $question = Question::factory()->locked()->create();
        $answerA = Answer::factory()->create(["text" => "answer A", "question_id" => $question->id]);
        $answerB = Answer::factory()->create(["text" => "answer B", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answerA);
        $question->save();

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answerA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answerB->id}/correct")
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answerA->id]);
    }

    public function testAdminCanChangeCorrectAnswerToInvalid(): void
    {
        $question = Question::factory()->create();
        $answer = Answer::factory()->create(["text" => "answer", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answer);
        $question->save();

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answer->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/invalid")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answer->id]);
    }

    public function testAdminCannotChangeCorrectAnswerToInvalidInLockedQuestion(): void
    {
        $question = Question::factory()->locked()->create();
        $answer = Answer::factory()->create(["text" => "answer", "question_id" => $question->id]);

        $question->correctAnswer()->associate($answer);
        $question->save();

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answer->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/invalid")
            ->assertStatus(403);

        $this->assertDatabaseHas("questions", ["correct_answer_id" => $answer->id]);
    }

    public function testAdminCanCopyAnswer(): void
    {
        $questionA = Question::factory()->create();
        $questionB = Question::factory()->create();
        $answer = Answer::factory()->create(["question_id" => $questionA->id]);

        $this->assertDatabaseHas("answers", ["question_id" => $questionA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/clone/{$questionB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["question_id" => $questionB->id]);
    }

    public function testAdminCanCopyLockedAnswer(): void
    {
        $questionA = Question::factory()->locked()->create();
        $questionB = Question::factory()->create();
        $answer = Answer::factory()->create(["question_id" => $questionA->id]);

        $this->assertDatabaseHas("answers", ["question_id" => $questionA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/clone/{$questionB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["question_id" => $questionB->id]);
    }

    public function testAdminCannotCopyAnswerToLockedQuestion(): void
    {
        $questionA = Question::factory()->create();
        $questionB = Question::factory()->locked()->create();
        $answer = Answer::factory()->create(["question_id" => $questionA->id]);

        $this->assertDatabaseHas("answers", ["question_id" => $questionA->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/clone/{$questionB->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("answers", ["question_id" => $questionA->id]);
    }

    public function testAdminCanCopyCorrectAnswer(): void
    {
        $questionA = Question::factory()->create();
        $questionB = Question::factory()->create();
        $answer = Answer::factory()->create(["question_id" => $questionA->id]);

        $questionA->correctAnswer()->associate($answer);
        $questionA->save();

        $this->assertDatabaseHas("answers", ["question_id" => $questionA->id]);
        $this->assertDatabaseHas("questions", ["id" => $questionA->id, "correct_answer_id" => $answer->id]);

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/clone/{$questionB->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["question_id" => $questionB->id]);
        $this->assertDatabaseHas("questions", ["id" => $questionA->id, "correct_answer_id" => $answer->id]);
        $this->assertDatabaseHas("questions", ["id" => $questionB->id, "correct_answer_id" => null]);
    }

    public function testAdminCannotCopyAnswerThatNotExisted(): void
    {
        $question = Question::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/2/clone/{$question->id}")
            ->assertStatus(404);
    }

    public function testAdminCannotCopyAnswerToQuestionThatNotExisted(): void
    {
        $answer = Answer::factory()->create();

        $this->actingAs($this->admin)
            ->from("/quizzes")
            ->post("/admin/answers/{$answer->id}/clone/2")
            ->assertStatus(404);
    }
}

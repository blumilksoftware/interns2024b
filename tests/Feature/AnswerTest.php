<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewQuestionAnswers(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();
        Answer::factory()->count(10)->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("answers", 10);

        $this->actingAs($user)
            ->get("/questions/{$question->id}/answers")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Answer/Index")
                    ->has("answers", 10),
            );
    }

    public function testUserCannotViewAnswersOfQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/questions/1/answers")
            ->assertStatus(404);
    }

    public function testUserCanViewSingleAnswer(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->create();

        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($user)
            ->get("/answers/{$answer->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Answer/Show")
                    ->where("answer.id", $answer->id),
            );
    }

    public function testUserCanViewLockedAnswer(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->locked()->create();

        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($user)
            ->get("/answers/{$answer->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Answer/Show")
                    ->where("answer.id", $answer->id)
                    ->where("answer.locked", true),
            );
    }

    public function testUserCannotViewAnswerThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/answers/1")
            ->assertStatus(404);
    }

    public function testUserCanCreateAnswer(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/answers", ["text" => "Example answer"])
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", [
            "text" => "Example answer",
            "question_id" => $question->id,
        ]);
    }

    public function testUserCanCreateMultipleAnswers(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/answers", ["text" => "Example answer 1"])
            ->assertRedirect("/quizzes");

        $this->from("/quizzes")
            ->post("/questions/{$question->id}/answers", ["text" => "Example answer 2"])
            ->assertRedirect("/quizzes");

        $this->from("/quizzes")
            ->post("/questions/{$question->id}/answers", ["text" => "Example answer 3"])
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["text" => "Example answer 1"]);
        $this->assertDatabaseHas("answers", ["text" => "Example answer 2"]);
        $this->assertDatabaseHas("answers", ["text" => "Example answer 2"]);
    }

    public function testUserCannotCreateAnswerToQuestionThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/1/answers", ["text" => "Example answer"])
            ->assertStatus(404);

        $this->assertDatabaseMissing("answers", [
            "text" => "Example answer",
        ]);
    }

    public function testUserCannotCreateAnswerToQuestionThatIsLocked(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->locked()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/answers", ["text" => "Example answer 1"])
            ->assertStatus(403);

        $this->assertDatabaseMissing("answers", [
            "text" => "Example answer",
        ]);
    }

    public function testUserCannotCreateInvalidAnswer(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->post("/questions/{$question->id}/answers", [])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->from("/quizzes")
            ->post("/questions/{$question->id}/answers", ["text" => false])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseCount("answers", 0);
    }

    public function testUserCanEditAnswer(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->create(["text" => "Old answer"]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->patch("/answers/{$answer->id}", ["text" => "New answer"])
            ->assertRedirect("/quizzes");

        $this->assertDatabaseHas("answers", ["text" => "New answer"]);
    }

    public function testUserCannotEditAnswerThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->patch("/answers/1", ["text" => "New answer"])
            ->assertStatus(404);
    }

    public function testUserCannotMakeInvalidEdit(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->create(["text" => "Old answer"]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->patch("/answers/{$answer->id}", [])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->from("/quizzes")
            ->patch("/answers/{$answer->id}", ["text" => true])
            ->assertRedirect("/quizzes")->assertSessionHasErrors(["text"]);

        $this->assertDatabaseHas("answers", ["text" => "Old answer"]);
    }

    public function testUserCannotEditLockedAnswer(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->locked()->create(["text" => "Old answer"]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->patch("/answers/{$answer->id}", ["text" => "New answer"])
            ->assertStatus(403);

        $this->assertDatabaseHas("answers", ["text" => "Old answer"]);
    }

    public function testUserCanDeleteAnswer(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->create(["text" => "answer"]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->delete("/answers/{$answer->id}")
            ->assertRedirect("/quizzes");

        $this->assertDatabaseMissing("answers", ["text" => "answer"]);
    }

    public function testUserCannotDeleteLockedAnswer(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->locked()->create(["text" => "answer"]);

        $this->actingAs($user)
            ->from("/quizzes")
            ->delete("/answers/{$answer->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("answers", ["text" => "answer"]);
    }

    public function testUserCannotDeleteAnswerThatNotExisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/quizzes")
            ->delete("/answers/1")
            ->assertStatus(404);
    }
}

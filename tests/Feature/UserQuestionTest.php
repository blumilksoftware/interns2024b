<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\CreateUserQuizAction;
use App\Models\Answer;
use App\Models\User;
use App\Models\UserQuiz;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserQuestionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CreateUserQuizAction $createUserQuiz;

    protected function setUp(): void
    {
        parent::setUp();
        UserQuiz::query()->truncate();

        $this->createUserQuiz = new CreateUserQuizAction();
        $this->user = User::factory()->create();
    }

    public function testUserCanAnswerQuestion(): void
    {
        $answer = Answer::factory()->locked()->create();
        $userQuiz = $this->createUserQuiz->execute($answer->question->quiz, $this->user);
        $userQuestion = $userQuiz->userQuestions[0];

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/questions/{$userQuestion->id}/{$answer->id}")
            ->assertRedirect("/");

        $this->assertDatabaseHas("user_questions", [
            "id" => $userQuestion->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionThatNotExisted(): void
    {
        $answer = Answer::factory()->locked()->create();

        $this->actingAs($this->user)
            ->patch("/questions/0/{$answer->id}")
            ->assertStatus(404);
    }

    public function testUserCannotAnswerQuestionThatIsNotTheirs(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->locked()->create();
        $userQuiz = $this->createUserQuiz->execute($answer->question->quiz, $user);
        $userQuestion = $userQuiz->userQuestions[0];

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/questions/{$userQuestion->id}/{$answer->id}")
            ->assertStatus(403);

        $this->assertDatabaseMissing("user_questions", [
            "id" => $userQuestion->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionThatBelongsToClosedUserQuiz(): void
    {
        $answer = Answer::factory()->locked()->create();
        $userQuiz = $this->createUserQuiz->execute($answer->question->quiz, $this->user);
        $userQuestion = $userQuiz->userQuestions[0];

        $userQuiz->closed_at = Carbon::now();
        $userQuiz->save();

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/questions/{$userQuestion->id}/{$answer->id}")
            ->assertStatus(403);

        $this->assertDatabaseMissing("user_questions", [
            "id" => $userQuestion->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionWithAnswerThatNotExist(): void
    {
        $answer = Answer::factory()->locked()->create();
        $userQuiz = $this->createUserQuiz->execute($answer->question->quiz, $this->user);
        $userQuestion = $userQuiz->userQuestions[0];

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/questions/{$userQuiz->id}/4")
            ->assertStatus(404);

        $this->assertDatabaseMissing("user_questions", [
            "id" => $userQuestion->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionWithAnswerNotAssignedToIt(): void
    {
        $answer = Answer::factory()->locked()->create();
        $userQuiz = $this->createUserQuiz->execute($answer->question->quiz, $this->user);
        $userQuestion = $userQuiz->userQuestions[0];

        $answer1 = Answer::factory()->locked()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/questions/{$userQuiz->id}/{$answer1->id}")
            ->assertStatus(403);

        $this->assertDatabaseMissing("user_questions", [
            "id" => $userQuestion->id,
            "answer_id" => $answer1->id,
        ]);
    }
}

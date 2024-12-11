<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\CreateUserQuestionAction;
use App\Actions\CreateUserQuizAction;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\PublishedQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateUserQuizAction $action;
    private User $user;
    private Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PublishedQuizSeeder::class);
        $this->action = new CreateUserQuizAction(new CreateUserQuestionAction());
        $this->quiz = Quiz::query()->firstOrFail();
        $this->user = User::factory()->create();
    }

    public function testUserQuizIsCreated(): void
    {
        $userQuiz = $this->action->execute($this->quiz, $this->user);

        $this->assertDatabaseHas("user_quizzes", [
            "id" => $userQuiz->id,
            "closed_at" => $this->quiz->closeAt,
            "quiz_id" => $this->quiz->id,
            "user_id" => $this->user->id,
        ]);

        $this->assertDatabaseCount("user_questions", 5);
    }
}

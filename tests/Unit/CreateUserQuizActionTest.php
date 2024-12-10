<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\CreateUserQuestionAction;
use App\Actions\CreateUserQuizAction;
use App\Jobs\CloseUserQuizJob;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\PublishedQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
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

        Queue::fake();
        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));

        $this->seed(PublishedQuizSeeder::class);
        $this->action = new CreateUserQuizAction(new CreateUserQuestionAction());
        $this->quiz = Quiz::query()->firstOrFail();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
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

    public function testCloseUserQuizJobIsDispatchedIfQuizIsClosingToday(): void
    {
        $this->quiz->scheduled_at = Carbon::now()->addHour();
        $this->quiz->save();

        $this->action->execute($this->quiz, $this->user);

        Queue::assertPushed(CloseUserQuizJob::class);
    }

    public function testCloseUserQuizJobIsNotDispatchedIfQuizIsAlreadyClosed(): void
    {
        $this->quiz->scheduled_at = Carbon::now()->subHours(3);
        $this->quiz->save();

        $this->action->execute($this->quiz, $this->user);

        Queue::assertNotPushed(CloseUserQuizJob::class);
    }

    public function testCloseUserQuizJobIsNotDispatchedIfQuizIsNotClosingToday(): void
    {
        $this->quiz->scheduled_at = Carbon::now()->addDay();
        $this->quiz->save();

        $this->action->execute($this->quiz, $this->user);

        Queue::assertNotPushed(CloseUserQuizJob::class);
    }
}

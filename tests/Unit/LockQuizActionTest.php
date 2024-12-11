<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\LockQuizAction;
use App\Jobs\CloseUserQuizJob;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LockQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private LockQuizAction $action;
    private Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));
        $this->action = new LockQuizAction();
        $this->quiz = Quiz::factory()->has(Question::factory()->locked())->create(["scheduled_at" => Carbon::now(), "duration" => 30]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testActionIsLockingQuiz(): void
    {
        $this->action->execute($this->quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $this->quiz->id,
            "locked_at" => "2024-10-11:08:00",
        ]);
    }

    public function testDispatchCloseUserQuizJobIfTestWillBeClosedToday(): void
    {
        Queue::fake();

        $this->action->execute($this->quiz);
        $this->artisan("app:dispatch-user-quiz-closed-event")->assertSuccessful();

        Queue::assertPushed(CloseUserQuizJob::class, fn($job) => $job->delay->eq($this->quiz->closeAt));
    }

    public function testNotDispatchCloseUserQuizJobIfTestWillNotBeClosedToday(): void
    {
        Queue::fake();

        $this->quiz->scheduled_at = Carbon::tomorrow();

        $this->action->execute($this->quiz);
        $this->artisan("app:dispatch-user-quiz-closed-event")->assertSuccessful();

        Queue::assertNothingPushed();
    }
}

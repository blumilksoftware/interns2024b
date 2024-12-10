<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\LockQuizAction;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LockQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private LockQuizAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));
        $this->action = new LockQuizAction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testActionIsLockingQuiz(): void
    {
        $quiz = Quiz::factory()->create();
        $this->action->execute($quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "locked_at" => "2024-10-11:08:00",
        ]);
    }
}

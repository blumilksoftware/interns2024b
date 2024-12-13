<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\UnlockQuizAction;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnlockQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private UnlockQuizAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UnlockQuizAction();
    }

    public function testActionIsLockingQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();
        $this->action->execute($quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "locked_at" => null,
        ]);
    }
}

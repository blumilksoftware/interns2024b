<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\UnpublishQuizRankingAction;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnpublishQuizRankingActionTest extends TestCase
{
    use RefreshDatabase;

    private UnpublishQuizRankingAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UnpublishQuizRankingAction();
    }

    public function testActionIsLockingQuiz(): void
    {
        $quiz = Quiz::factory()->withRanking()->create();
        $this->action->execute($quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "ranking_published_at" => null,
        ]);
    }
}

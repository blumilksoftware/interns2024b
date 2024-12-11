<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\PublishQuizRankingAction;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublishQuizRankingActionTest extends TestCase
{
    use RefreshDatabase;

    private PublishQuizRankingAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));
        $this->action = new PublishQuizRankingAction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testActionIsPublishingQuiz(): void
    {
        $quiz = Quiz::factory()->create();
        $this->action->execute($quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "ranking_published_at" => "2024-10-11:08:00",
        ]);
    }
}

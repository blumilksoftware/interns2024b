<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\CloseUserQuizAction;
use App\Events\UserQuizClosed;
use App\Models\UserQuiz;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CloseUserQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private CloseUserQuizAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));
        Event::fake();
        $this->action = new CloseUserQuizAction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testActionIsClosingUserQuiz(): void
    {
        $quiz = UserQuiz::factory()->create();

        $this->action->execute($quiz);

        Event::assertDispatched(UserQuizClosed::class);
        $this->assertDatabaseHas("user_quizzes", ["id" => $quiz->id, "closed_at" => "2024-10-11:08:00"]);
    }
}

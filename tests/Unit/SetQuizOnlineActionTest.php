<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\SetQuizOnlineAction;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetQuizOnlineActionTest extends TestCase
{
    use RefreshDatabase;

    private SetQuizOnlineAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new SetQuizOnlineAction();
    }

    public function testActionSetQuizLocal(): void
    {
        $quiz = Quiz::factory()->local()->create();
        $this->action->execute($quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "is_local" => false,
        ]);
    }
}

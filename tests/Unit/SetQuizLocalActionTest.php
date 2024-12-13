<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\SetQuizLocalAction;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetQuizLocalActionTest extends TestCase
{
    use RefreshDatabase;

    private SetQuizLocalAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new SetQuizLocalAction();
    }

    public function testActionSetQuizLocal(): void
    {
        $quiz = Quiz::factory()->create();
        $this->action->execute($quiz);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "is_local" => true,
        ]);
    }
}

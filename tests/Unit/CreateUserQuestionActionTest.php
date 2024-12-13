<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\CreateUserQuestionAction;
use App\Models\Question;
use App\Models\UserQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserQuestionActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateUserQuestionAction $action;
    private UserQuiz $userQuiz;
    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreateUserQuestionAction();
        $this->userQuiz = UserQuiz::factory()->create();
        $this->question = Question::factory()->create();
    }

    public function testUserQuestionIsCreated(): void
    {
        $userQuestion = $this->action->execute($this->question, $this->userQuiz);

        $this->assertDatabaseHas("user_questions", [
            "id" => $userQuestion->id,
            "user_quiz_id" => $this->userQuiz->id,
            "question_id" => $this->question->id,
        ]);
    }
}

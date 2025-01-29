<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\UndisqualifyUserAction;
use App\Models\Disqualification;
use App\Models\UserQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UndisqualifyUserActionTest extends TestCase
{
    use RefreshDatabase;

    private UndisqualifyUserAction $action;
    private UserQuiz $userQuiz;
    private Disqualification $disqualification;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userQuiz = UserQuiz::factory()->closed()->create();
        $this->disqualification = new Disqualification();
        $this->disqualification->reason = "Reason";

        $this->disqualification->userQuiz()->associate($this->userQuiz);
        $this->disqualification->save();

        $this->action = new UndisqualifyUserAction();
    }

    public function testUndisqualifyUser(): void
    {
        $this->action->execute($this->userQuiz);

        $this->assertDatabaseMissing("disqualifications", [
            "id" => $this->disqualification->id,
        ]);
    }
}

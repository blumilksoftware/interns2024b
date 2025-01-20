<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\CreateAdminAction;
use App\Actions\DisqualifyUserAction;
use App\Actions\UndisqualifyUserAction;
use App\Models\Disqualification;
use App\Models\School;
use App\Models\User;
use App\Models\UserQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisqualifyUserActionTest extends TestCase
{
    use RefreshDatabase;

    private DisqualifyUserAction $action;
    private UserQuiz $userQuiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userQuiz = UserQuiz::factory()->closed()->create();
        $this->action = new DisqualifyUserAction();
    }

    public function testDisqualifyUser(): void
    {
        $this->action->execute($this->userQuiz, "reason");

        $this->assertDatabaseHas("disqualifications", [
            "reason" => "reason"
        ]);
    }
}

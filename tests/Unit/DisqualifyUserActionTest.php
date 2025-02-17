<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\DisqualifyUserAction;
use App\Models\UserQuiz;
use App\Notifications\DisqualificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

    public function testDisqualifyUserWithoutEmailNotification(): void
    {
        Notification::fake();

        $this->action->execute($this->userQuiz, "reason");

        $this->assertDatabaseHas("disqualifications", [
            "reason" => "reason",
        ]);

        Notification::assertNotSentTo($this->userQuiz->user, DisqualificationNotification::class);
    }

    public function testDisqualifyUserWithEmailNotification(): void
    {
        Notification::fake();

        $this->action->execute($this->userQuiz, "reason", true);

        $this->assertDatabaseHas("disqualifications", [
            "reason" => "reason",
        ]);

        Notification::assertSentTo($this->userQuiz->user, DisqualificationNotification::class);
    }
}

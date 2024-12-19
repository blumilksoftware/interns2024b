<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\SendInviteJob;
use App\Models\Quiz;
use App\Models\User;
use App\Notifications\InviteUserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendInviteJobTest extends TestCase
{
    use RefreshDatabase;

    protected Quiz $quiz;
    protected User $user;
    protected SendInviteJob $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quiz = Quiz::factory()->locked()->create();
        $this->user = User::factory()->create();
        $this->job = new SendInviteJob($this->user, $this->quiz);
    }

    public function testSendsInviteNotificationWhenUserIsAssignedToQuiz(): void
    {
        Notification::fake();

        $this->quiz->assignedUsers()->attach($this->user);
        $this->job->handle();

        Notification::assertSentTo($this->user, InviteUserNotification::class);
    }

    public function testDoesNotSendInviteNotificationWhenUserIsNotAssignedToQuiz(): void
    {
        Notification::fake();

        $this->job->handle();

        Notification::assertNotSentTo($this->user, InviteUserNotification::class);
    }
}

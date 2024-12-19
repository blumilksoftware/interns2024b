<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\SendInviteJob;
use App\Models\Quiz;
use App\Models\User;
use App\Notifications\InviteUserNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
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

        Bus::fake();
        Cache::shouldReceive("get")->andReturn(null);
        Cache::shouldReceive("set")->andReturn(true);

        $this->now = Carbon::create(2024, 12, 19, 12, 0, 0);
        Carbon::setTestNow($this->now);

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

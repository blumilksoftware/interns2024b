<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Events\UserInvitedToQuiz;
use App\Jobs\SendInviteJob;
use App\Listeners\SendInvitedToQuizNotification;
use App\Models\Quiz;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Testing\Fakes\BatchFake;
use Mockery;
use Tests\TestCase;

class SendInvitedToQuizNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Quiz $quiz;
    protected User $inviter;
    protected User $invitee;
    protected Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $this->now = Carbon::create(2024, 12, 19, 12, 0, 0);
        Carbon::setTestNow($this->now);

        $this->quiz = Quiz::factory()->locked()->create();
        $this->inviter = User::factory()->admin()->create();
        $this->invitee = User::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testHandleDispatchesSendInviteJob(): void
    {
        $listener = new SendInvitedToQuizNotification();
        $listener->handle(new UserInvitedToQuiz($this->quiz, $this->invitee, $this->inviter));

        $cacheKey = "send_invite-" . $this->quiz->id . "-" . $this->invitee->id;
        $cacheValue = Cache::get($cacheKey);

        $this->assertNotNull($cacheValue);
        $this->assertIsString($cacheValue);

        Bus::assertBatched(function ($batch) {
            $job = $batch->jobs->first();

            return $job instanceof SendInviteJob
                && $job->getUser() === $this->invitee
                && $job->getQuiz() === $this->quiz
                && $this->now->addMinutes(15)->eq($job->delay);
        });
    }

    public function testHandleCancelsExistingBatch(): void
    {
        $fakeBatch = Mockery::mock(BatchFake::class);
        $fakeBatch->shouldReceive("cancel");

        $busName = "send_invite-" . $this->quiz->id . "-" . $this->invitee->id;
        Cache::put($busName, $fakeBatch->id);

        Bus::dispatch($fakeBatch);

        $listener = new SendInvitedToQuizNotification();

        $event = new UserInvitedToQuiz($this->quiz, $this->invitee, $this->inviter);

        $listener->handle($event);

        Bus::assertBatched(function ($batch) {
            $job = $batch->jobs->first();

            return $job instanceof SendInviteJob
                && $job->getUser() === $this->invitee
                && $job->getQuiz() === $this->quiz
                && $this->now->addMinutes(15)->eq($job->delay);
        });
    }
}

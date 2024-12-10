<?php

declare(strict_types=1);

namespace Feature\Commands;

use App\Events\AssignedQuizClosed;
use App\Events\UserQuizClosed;
use App\Jobs\CloseUserQuizJob;
use App\Listeners\SendAssignedQuizClosedNotification;
use App\Listeners\SendQuizClosedNotification;
use App\Models\Quiz;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PublishedQuizSeeder;
use Database\Seeders\UserQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DispatchUserQuizClosedEventTest extends TestCase
{
    use RefreshDatabase;

    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));

        $this->seed(PublishedQuizSeeder::class);
        $this->quiz = Quiz::query()->firstOrFail();
        $this->quiz->duration = 30;
        $this->quiz->save();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testDispatchCloseUserQuizJob(): void
    {
        Queue::fake();

        $this->quiz->scheduled_at = Carbon::now()->addHour();
        $this->quiz->save();

        $this->artisan("app:dispatch-user-quiz-closed-event")->assertSuccessful();

        Queue::assertPushed(CloseUserQuizJob::class, fn($job) => $job->delay->eq($this->quiz->closeAt));
    }

    public function testUserQuizClosedEventIsDispatched(): void
    {
        Event::fake([UserQuizClosed::class, AssignedQuizClosed::class]);

        $this->quiz->scheduled_at = Carbon::now()->addHour();
        $this->quiz->save();

        $this->quiz->assignedUsers()->attach(User::factory()->create());
        UserQuizSeeder::createUserQuizForUser($this->quiz, User::factory()->create(), 2);

        $this->artisan("app:dispatch-user-quiz-closed-event")->assertSuccessful();
        Carbon::setTestNow($this->quiz->closeAt->addHour());

        Event::assertListening(UserQuizClosed::class, SendQuizClosedNotification::class);
        Event::assertListening(AssignedQuizClosed::class, SendAssignedQuizClosedNotification::class);
    }

    public function testSkipAlreadyClosedQuizzes(): void
    {
        Queue::fake();

        $this->quiz->scheduled_at = Carbon::now()->subDays(2);
        $this->quiz->save();
        $this->quiz->refresh();

        $this->artisan("app:dispatch-user-quiz-closed-event")->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function testSkipQuizzesThatWillNotBeClosedToday(): void
    {
        Queue::fake();

        $this->quiz->scheduled_at = Carbon::now()->addDay();
        $this->quiz->save();
        $this->quiz->refresh();

        $this->artisan("app:dispatch-user-quiz-closed-event")->assertSuccessful();

        Queue::assertNothingPushed();
    }
}

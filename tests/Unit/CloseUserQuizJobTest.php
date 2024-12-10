<?php

declare(strict_types=1);

namespace Tests\Unit;

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
use Tests\TestCase;

class CloseUserQuizJobTest extends TestCase
{
    use RefreshDatabase;

    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PublishedQuizSeeder::class);
        $this->quiz = Quiz::query()->firstOrFail();
    }

    public function testEmitUserQuizClosedEvent(): void
    {
        Event::fake([UserQuizClosed::class, AssignedQuizClosed::class]);

        UserQuizSeeder::createUserQuizForUser($this->quiz, User::factory()->create(), 2);
        CloseUserQuizJob::dispatch($this->quiz);

        Event::assertListening(UserQuizClosed::class, SendQuizClosedNotification::class);
        Event::assertNotDispatched(AssignedQuizClosed::class);
    }

    public function testSkipNotLockedQuizzes(): void
    {
        Event::fake([UserQuizClosed::class, AssignedQuizClosed::class]);

        $this->quiz->locked_at = null;
        $this->quiz->save();

        CloseUserQuizJob::dispatch($this->quiz);

        Event::assertNotDispatched(UserQuizClosed::class);
        Event::assertNotDispatched(AssignedQuizClosed::class);
    }

    public function testSkipManuallyClosedQuizzes(): void
    {
        Event::fake([UserQuizClosed::class, AssignedQuizClosed::class]);

        $userQuiz = UserQuizSeeder::createUserQuizForUser($this->quiz, User::factory()->create(), 2);
        $userQuiz->closed_at = Carbon::now();
        $userQuiz->save();

        CloseUserQuizJob::dispatch($this->quiz);

        Event::assertNotDispatched(UserQuizClosed::class);
        Event::assertNotDispatched(AssignedQuizClosed::class);
    }

    public function testEmitAssignedQuizClosedEventToUsersThatWereAssignedButDidNotParticipateInQuiz(): void
    {
        Event::fake([UserQuizClosed::class, AssignedQuizClosed::class]);

        $this->quiz->assignedUsers()->attach(User::factory()->create());

        CloseUserQuizJob::dispatch($this->quiz);

        Event::assertNotDispatched(UserQuizClosed::class);
        Event::assertListening(AssignedQuizClosed::class, SendAssignedQuizClosedNotification::class);
    }
}

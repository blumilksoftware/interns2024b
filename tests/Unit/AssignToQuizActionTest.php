<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\AssignToQuizAction;
use App\Events\UserInvitedToQuiz;
use App\Listeners\SendInvitedToQuizNotification;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

use function collect;

class AssignToQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private AssignToQuizAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new AssignToQuizAction();
    }

    public function testUsersAreAssignedToQuiz(): void
    {
        User::factory()->count(10)->create();
        $quiz = Quiz::factory()->create();

        $this->assertEquals(0, $quiz->assignedUsers()->count());
        $this->action->execute($quiz, User::all()->pluck("id"), User::factory()->create());
        $this->assertEquals(10, $quiz->assignedUsers()->count());
    }

    public function testAlreadyAssignedUsersAreSkipped(): void
    {
        $user = User::factory()->create();
        User::factory()->count(10)->create();
        $quiz = Quiz::factory()->create();

        $this->action->execute($quiz, User::all()->pluck("id"), $user);
        $this->assertEquals(11, $quiz->assignedUsers()->count());

        $this->action->execute($quiz, User::all()->pluck("id"), $user);
        $this->assertEquals(11, $quiz->assignedUsers()->count());
    }

    public function testUnknownUsersAreSkipped(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();

        $this->action->execute($quiz, collect([$user->id, 2, 3, 4, 5]), User::factory()->create());
        $this->assertEquals(1, $quiz->assignedUsers()->count());
    }

    public function testUserInvitedToQuizEventIsDispatched(): void
    {
        Event::fake([UserInvitedToQuiz::class]);

        User::factory()->count(10)->create();
        $quiz = Quiz::factory()->create();

        $this->assertEquals(0, $quiz->assignedUsers()->count());
        $this->action->execute($quiz, User::all()->pluck("id"), User::factory()->create());
        $this->assertEquals(10, $quiz->assignedUsers()->count());

        Event::assertListening(UserInvitedToQuiz::class, SendInvitedToQuizNotification::class);
    }
}

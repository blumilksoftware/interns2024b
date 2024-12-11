<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\UnassignFromQuizAction;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function collect;

class UnassignFromQuizActionTest extends TestCase
{
    use RefreshDatabase;

    private UnassignFromQuizAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UnassignFromQuizAction();
    }

    public function testUsersAreUnassignedFromQuiz(): void
    {
        User::factory()->count(10)->create();
        $quiz = Quiz::factory()->create();
        $quiz->assignedUsers()->attach(User::all());

        $this->assertEquals(10, $quiz->assignedUsers()->count());
        $this->action->execute($quiz, User::all()->pluck("id"));
        $this->assertEquals(0, $quiz->assignedUsers()->count());
    }

    public function testUnassignedUsersAreSkipped(): void
    {
        User::factory()->count(10)->create();
        $quiz = Quiz::factory()->create();

        $this->action->execute($quiz, User::all()->pluck("id"));
        $this->assertEquals(0, $quiz->assignedUsers()->count());

        $this->action->execute($quiz, User::all()->pluck("id"));
        $this->assertEquals(0, $quiz->assignedUsers()->count());
    }

    public function testUnknownUsersAreSkipped(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $quiz->assignedUsers()->attach($user);

        $this->assertEquals(1, $quiz->assignedUsers()->count());
        $this->action->execute($quiz, collect([$user->id, 2, 3, 4, 5]));
        $this->assertEquals(0, $quiz->assignedUsers()->count());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class QuizModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2024, 10, 11, 8));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testIsLocked(): void
    {
        $quiz1 = Quiz::factory()->create(["locked_at" => Carbon::now()]);
        $quiz2 = Quiz::factory()->create(["locked_at" => null]);

        $this->assertTrue($quiz1->isLocked);
        $this->assertFalse($quiz2->isLocked);
    }

    public function testIsPublished(): void
    {
        $quiz1 = Quiz::factory()->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->subHour()]);
        $quiz2 = Quiz::factory()->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->addHour()]);
        $quiz3 = Quiz::factory()->create(["locked_at" => null, "scheduled_at" => Carbon::now()->subHour()]);

        $this->assertTrue($quiz1->isPublished);
        $this->assertFalse($quiz2->isPublished);
        $this->assertFalse($quiz3->isPublished);
    }

    public function testState(): void
    {
        $published = Quiz::factory()->create(["locked_at" => Carbon::now(), "duration" => 30, "scheduled_at" => Carbon::now()->subHour()]);
        $locked = Quiz::factory()->create(["locked_at" => Carbon::now(), "duration" => 30, "scheduled_at" => Carbon::now()->addHour()]);
        $unlocked = Quiz::factory()->create(["locked_at" => null, "scheduled_at" => null]);

        $this->assertEquals("published", $published->state);
        $this->assertEquals("locked", $locked->state);
        $this->assertEquals("unlocked", $unlocked->state);
    }

    public function testIsUserAssigned(): void
    {
        $user = User::factory()->create();
        $quiz1 = Quiz::factory()->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->addHour()]);
        $quiz2 = Quiz::factory()->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->addHour()]);
        $quiz1->assignedUsers()->attach($user);

        $this->assertTrue($quiz1->isUserAssigned($user));
        $this->assertFalse($quiz2->isUserAssigned($user));
    }

    public function testIsRankingPublished(): void
    {
        $quiz1 = Quiz::factory()->create(["ranking_published_at" => Carbon::now()]);
        $quiz2 = Quiz::factory()->create(["ranking_published_at" => null]);

        $this->assertTrue($quiz1->isRankingPublished);
        $this->assertFalse($quiz2->isRankingPublished);
    }

    public function testCanBeUnlocked(): void
    {
        $quiz1 = Quiz::factory()->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->addHour()]);
        $quiz2 = Quiz::factory()->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->subHour()]);
        $quiz3 = Quiz::factory()->create(["locked_at" => null, "scheduled_at" => null]);

        $this->assertTrue($quiz1->canBeUnlocked);
        $this->assertFalse($quiz2->canBeUnlocked);
        $this->assertFalse($quiz3->canBeUnlocked);
    }

    public function testCanBeLocked(): void
    {
        $quiz1 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => null, "scheduled_at" => Carbon::now()->addHour(), "duration" => 30]);
        $quiz2 = Quiz::factory()->create(["locked_at" => null, "scheduled_at" => Carbon::now()->addHour(), "duration" => 30]);
        $quiz3 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => Carbon::now(), "scheduled_at" => Carbon::now()->addHour(), "duration" => 30]);
        $quiz4 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => null, "scheduled_at" => Carbon::now()->subHour(), "duration" => 30]);
        $quiz5 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => null, "scheduled_at" => Carbon::now()->addHour(), "duration" => null]);

        $this->assertTrue($quiz1->canBeLocked);
        $this->assertFalse($quiz2->canBeLocked);
        $this->assertFalse($quiz3->canBeLocked);
        $this->assertFalse($quiz4->canBeLocked);
        $this->assertFalse($quiz5->canBeLocked);
    }

    public function testCloseAt(): void
    {
        $quiz1 = Quiz::factory()->has(Question::factory()->locked())->create(["scheduled_at" => Carbon::now(), "duration" => 30]);
        $quiz2 = Quiz::factory()->create(["scheduled_at" => null]);

        $this->assertEquals("2024-10-11:08:30", $quiz1->closeAt->format("Y-m-d:H:i"));
        $this->assertNull($quiz2->closeAt);
    }

    public function testIsReadyToBePublished(): void
    {
        $quiz1 = Quiz::factory()->has(Question::factory()->locked())->create(["scheduled_at" => Carbon::now()->addHour(), "duration" => 30]);
        $quiz2 = Quiz::factory()->create(["scheduled_at" => Carbon::now()->addHour(), "duration" => 30]);
        $quiz3 = Quiz::factory()->has(Question::factory())->create(["scheduled_at" => Carbon::now()->addHour(), "duration" => 30]);
        $quiz4 = Quiz::factory()->has(Question::factory()->locked())->create(["scheduled_at" => Carbon::now()->addHour(), "duration" => null]);

        $this->assertTrue($quiz1->isReadyToBePublished());
        $this->assertFalse($quiz2->isReadyToBePublished());
        $this->assertFalse($quiz3->isReadyToBePublished());
        $this->assertFalse($quiz4->isReadyToBePublished());
    }

    public function testHasUserQuizzesFrom(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $userQuiz = UserQuiz::factory(["user_id" => $user1])->create();

        $this->assertTrue($userQuiz->quiz->hasUserQuizzesFrom($user1));
        $this->assertFalse($userQuiz->quiz->hasUserQuizzesFrom($user2));
    }

    public function testIsClosingToday(): void
    {
        $quiz1 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => Carbon::now(), "duration" => 30, "scheduled_at" => Carbon::now()]);
        $quiz2 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => Carbon::now(), "duration" => 30, "scheduled_at" => Carbon::now()->addDay()]);
        $quiz3 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => Carbon::now(), "duration" => 30, "scheduled_at" => Carbon::now()->subHour()]);
        $quiz4 = Quiz::factory()->has(Question::factory()->locked())->create(["locked_at" => null, "duration" => 30, "scheduled_at" => Carbon::now()]);

        $this->assertTrue($quiz1->isClosingToday());
        $this->assertFalse($quiz2->isClosingToday());
        $this->assertFalse($quiz3->isClosingToday());
        $this->assertFalse($quiz4->isClosingToday());
    }
}

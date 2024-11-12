<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\UserQuiz;
use App\Models\User;
use Database\Seeders\UserQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected UserQuizSeeder $seeder;
    protected User $admin;
    protected Quiz $quiz;
    protected Quiz $unlockedQuiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seeder = new UserQuizSeeder();
        $this->seeder->run();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        $this->quiz = $this->seeder->quiz;
        $this->seeder->createUserQuizForUser($this->user, 2);

        $this->unlockedQuiz = Quiz::factory()->create();
    }

    public function testUserHasPointsInAnsweredQuiz(): void
    {
        $userQuiz = UserQuiz::where("user_id", $this->user->id)->first();

        $userQuiz->refresh();

        $this->assertGreaterThanOrEqual(0, $userQuiz->points);
        $this->assertLessThanOrEqual($userQuiz->maxPoints, $userQuiz->points);
    }

    public function testUserPointsAreCalculatedCorrectly(): void
    {
        $user2 = User::factory()->create();
        $this->seeder->createUserQuizForUser($user2, 3);

        $userQuiz1 = UserQuiz::where("user_id", $this->user->id)
            ->where("quiz_id", $this->quiz->id)
            ->first();

        $userQuiz2 = UserQuiz::where("user_id", $user2->id)
            ->where("quiz_id", $this->quiz->id)
            ->first();

        $userQuiz1->refresh();
        $userQuiz2->refresh();

        $userScore = $userQuiz1->points;
        $user2Score = $userQuiz2->points;

        $this->assertEquals(2, $userScore);
        $this->assertEquals(3, $user2Score);
    }

    public function testAdminCanViewLockedQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quiz->id}/ranking")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/Ranking"),
            );
    }

    public function testAdminCannotViewUnlockedQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->unlockedQuiz->id}/ranking")
            ->assertForbidden();
    }

    public function testAdminCanPublishLockedQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/ranking/publish")
            ->assertSessionHas("status", "Ranking został opublikowany.");

        $this->quiz->refresh();
        $this->assertNotNull($this->quiz->ranking_published_at);
    }

    public function testAdminCannotPublishUnlockedQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->unlockedQuiz->id}/ranking/publish")
            ->assertForbidden();
    }

    public function testAdminCanUnpublishLockedQuizRanking(): void
    {
        $this->quiz->ranking_published_at = now();
        $this->quiz->save();

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/ranking/unpublish")
            ->assertSessionHas("status", "Ranking został wycofany.");

        $this->quiz->refresh();
        $this->assertNull($this->quiz->ranking_published_at);
    }

    public function testAdminCannotUnpublishUnlockedQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->unlockedQuiz->id}/ranking/unpublish")
            ->assertForbidden();
    }

    public function testAdminCannotViewNotExistingQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/quizzes/9999/ranking/")
            ->assertNotFound();
    }

    public function testAdminCannotPublishNotExistingQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/9999/ranking/publish")
            ->assertNotFound();
    }

    public function testAdminCannotPublishEmptyQuizRanking(): void
    {
        $emptyQuiz = Quiz::factory()->locked()->create();

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$emptyQuiz->id}/ranking/publish")
            ->assertForbidden();
    }

    public function testAdminCannotUnpublishNotExistingQuizRanking(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/9999/ranking/unpublish")
            ->assertNotFound();
    }

    public function testUserCanViewPublishedQuizRankingHeParticipated(): void
    {
        $this->quiz->ranking_published_at = now();
        $this->quiz->save();

        $this->actingAs($this->user)
            ->get("/quizzes/{$this->quiz->id}/ranking/")
            ->assertInertia(fn(Assert $page) => $page->component("User/Ranking"));
    }

    public function testUserCannotViewUnpublishedQuizRankingHeParticipated(): void
    {
        $this->actingAs($this->user)
            ->get("/quizzes/{$this->quiz->id}/ranking")
            ->assertForbidden();
    }

    public function testUserCannotViewPublishedQuizRankingHeDidNotParticipated(): void
    {
        $userWithoutUserQuizzes = User::factory()->create();

        $this->quiz->ranking_published_at = now();
        $this->quiz->save();

        $this->actingAs($userWithoutUserQuizzes)
            ->get("/quizzes/{$this->quiz->id}/ranking")
            ->assertForbidden();
    }

    public function testUserCannotViewUnpublishedQuizRankingHeDidNotParticipated(): void
    {
        $this->actingAs($this->user)
            ->get("/quizzes/{$this->unlockedQuiz->id}/ranking")
            ->assertForbidden();
    }

    public function testUserCannotViewQuizRankingThatQuizDoesNotExist(): void
    {
        $this->actingAs($this->user)
            ->get("/quizzes/9999/ranking")
            ->assertNotFound();
    }

    public function testUserCannotAccessToAdminRanking(): void
    {
        $this->actingAs($this->user)
            ->get("/admin/quizzes/{$this->quiz->id}/ranking")
            ->assertForbidden();

        $this->actingAs($this->user)
            ->post("/admin/quizzes/{$this->quiz->id}/ranking/publish")
            ->assertForbidden();

        $this->actingAs($this->user)
            ->post("/admin/quizzes/{$this->quiz->id}/ranking/unpublish")
            ->assertForbidden();
    }
}

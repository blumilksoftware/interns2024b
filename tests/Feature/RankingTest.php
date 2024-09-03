<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use Database\Seeders\UserQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected User $user3;
    protected User $admin;
    protected Quiz $quiz2;

    protected function setUp(): void
    {
        parent::setUp();

        $seeder = new UserQuizSeeder();
        $seeder->run();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->user3 = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->quiz = $seeder->quiz;
        $seeder->createSubmissionForUser($this->user1, 2);
        $seeder->createSubmissionForUser($this->user2, 3);
        $this->quiz2 = Quiz::factory()->create();
    }

    public function testUserHasPointsInAnsweredQuiz(): void
    {
        $quizSubmission = QuizSubmission::where("user_id", $this->user1->id)->first();

        $quizSubmission->refresh();

        $this->assertGreaterThanOrEqual(0, $quizSubmission->points);
        $this->assertLessThanOrEqual($quizSubmission->maxPoints, $quizSubmission->points);
    }

    public function testUserPointsAreCalculatedCorrectly(): void
    {
        $quizSubmission1 = QuizSubmission::where("user_id", $this->user1->id)
            ->where("quiz_id", $this->quiz->id)
            ->first();

        $quizSubmission2 = QuizSubmission::where("user_id", $this->user2->id)
            ->where("quiz_id", $this->quiz->id)
            ->first();

        $quizSubmission1->refresh();
        $quizSubmission2->refresh();

        $user1Score = $quizSubmission1->points;
        $user2Score = $quizSubmission2->points;

        $this->assertEquals(2, $user1Score);
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
            ->get("/admin/quizzes/{$this->quiz2->id}/ranking")
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
            ->post("/admin/quizzes/{$this->quiz2->id}/ranking/publish")
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
            ->post("/admin/quizzes/{$this->quiz2->id}/ranking/unpublish")
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

        $this->actingAs($this->user1)
            ->get("/quizzes/{$this->quiz->id}/ranking/")
            ->assertInertia(fn(Assert $page) => $page->component("User/Ranking"));
    }

    public function testUserCannotViewUnpublishedQuizRankingHeParticipated(): void
    {
        $this->actingAs($this->user1)
            ->get("/quizzes/{$this->quiz->id}/ranking")
            ->assertForbidden();
    }

    public function testUserCannotViewPublishedQuizRankingHeDidNotParticipated(): void
    {
        $this->quiz->ranking_published_at = now();
        $this->quiz->save();

        $this->actingAs($this->user3)
            ->get("/quizzes/{$this->quiz->id}/ranking")
            ->assertForbidden();
    }

    public function testUserCannotViewUnpublishedQuizRankingHeDidNotParticipated(): void
    {
        $this->actingAs($this->user1)
            ->get("/quizzes/{$this->quiz2->id}/ranking")
            ->assertForbidden();
    }

    public function testUserCannotViewQuizRankingThatQuizDoesNotExist(): void
    {
        $this->actingAs($this->user1)
            ->get("/quizzes/9999/ranking")
            ->assertNotFound();
    }

    public function testUserCannotAccessToAdminRanking(): void
    {
        $this->actingAs($this->user1)
            ->get("/admin/quizzes/{$this->quiz->id}/ranking")
            ->assertForbidden();

        $this->actingAs($this->user1)
            ->post("/admin/quizzes/{$this->quiz->id}/ranking/publish")
            ->assertForbidden();

        $this->actingAs($this->user1)
            ->post("/admin/quizzes/{$this->quiz->id}/ranking/unpublish")
            ->assertForbidden();
    }
}

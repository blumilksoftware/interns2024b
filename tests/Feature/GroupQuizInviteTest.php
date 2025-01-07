<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\UserQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GroupQuizInviteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Quiz $quizFrom;
    protected Quiz $quizTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserQuizSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        $this->quizTo = Quiz::query()->firstOrFail();
        $this->quizTo->locked_at = now();
        $this->quizTo->scheduled_at = now()->addMinutes(10);
        $this->quizTo->ranking_published_at = null;
        $this->quizTo->save();

        $this->quizFrom = Quiz::factory()->create();
        $this->quizFrom->locked_at = now();
        $this->quizFrom->scheduled_at = now()->subDay();
        $this->quizFrom->save();
    }

    public function testAdminCanViewGroupInvitePanelForLockedToQuiz(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quizFrom->id}/ranking/invite/{$this->quizTo->id}")
            ->assertInertia(fn(Assert $page) => $page->component("Admin/GroupInvite"));
    }

    public function testAdminCannotViewGroupInvitePanelForPublishedToQuiz(): void
    {
        $this->quizTo->locked_at = now()->subMinutes(30);
        $this->quizTo->scheduled_at = now();
        $this->quizTo->ranking_published_at = now();
        $this->quizTo->save();

        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quizFrom->id}/ranking/invite/{$this->quizTo->id}")
            ->assertForbidden();
    }

    public function testAdminCannotViewGroupInvitePanelForUnpublishedFromQuiz(): void
    {
        $this->quizFrom->locked_at = null;
        $this->quizFrom->save();

        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quizFrom->id}/ranking/invite/{$this->quizTo->id}")
            ->assertForbidden();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\AssignToQuizAction;
use App\Models\Quiz;
use App\Models\School;
use App\Models\User;
use Database\Seeders\UserQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class QuizInviteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Quiz $quiz;
    protected Quiz $unlockedQuiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserQuizSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        $this->quiz = Quiz::query()->firstOrFail();
        $this->quiz->locked_at = now();
        $this->quiz->scheduled_at = now()->addMinutes(10);
        $this->quiz->ranking_published_at = null;
        $this->quiz->save();
    }

    public function testAdminCanViewInvitePanelForLockedQuiz(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quiz->id}/invite")
            ->assertInertia(
                fn(Assert $page) => $page->component("Admin/Invite")
                    ->has("users.data", 3),
            );
    }

    public function testAdminCannotViewInvitePanelInPublishedQuiz(): void
    {
        $this->quiz->locked_at = now()->subMinutes(30);
        $this->quiz->scheduled_at = now();
        $this->quiz->ranking_published_at = now();
        $this->quiz->save();

        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quiz->id}/invite")
            ->assertForbidden();
    }

    public function testFilteringAndSortingUsers(): void
    {
        $school = School::factory()->create();
        User::factory()->count(8)->create(["firstname" => "test"]);
        $user = User::factory()->create([
            "firstname" => "Jan",
            "school_id" => $school->id,
        ]);

        $this->actingAs($this->admin)
            ->get("/admin/quizzes/{$this->quiz->id}/invite?search={$user->firstname}&sort=name&order=asc&schoolId={$school->id}")
            ->assertInertia(fn(Assert $page) => $page
                ->component("Admin/Invite")
                ->has("users.data", 1));
    }

    public function testAssigningUsersToQuiz(): void
    {
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck("id")->toArray();

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/assign", ["ids" => $userIds])
            ->assertRedirect()
            ->assertSessionHas("status", "Użytkownicy zostali przypisani do testu.  Za 15 minut zostaną o tym powiadomieni drogą mailową. Jeżeli w ciągu 15 minut anulujesz zaproszenie, mail nie zostanie wysłany.");

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas("quiz_assignments", [
                "quiz_id" => $this->quiz->id,
                "user_id" => $userId,
            ]);
        }

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/assign", ["ids" => $userIds])
            ->assertRedirect()->assertSessionHas("status", "Użytkownicy zostali przypisani do testu.  Za 15 minut zostaną o tym powiadomieni drogą mailową. Jeżeli w ciągu 15 minut anulujesz zaproszenie, mail nie zostanie wysłany.");
    }

    public function testSkipAlreadyAssignedUsersWhileAssigningToQuiz(): void
    {
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck("id")->toArray();

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/assign", ["ids" => $userIds]);

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/assign", ["ids" => $userIds]);

        $this->assertDatabaseCount("quiz_assignments", 2);
    }

    public function testInvitingWithEmptyUserIds(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite", ["ids" => []]);
        $this->assertDatabaseCount("quiz_assignments", 0);
    }

    public function testAdminCanUnassignAssignedUserFromQuiz(): void
    {
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck("id");

        (new AssignToQuizAction())->execute($this->quiz, $userIds, $this->admin);
        $this->assertDatabaseCount("quiz_assignments", 2);

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/unassign", ["ids" => $userIds]);

        $this->assertDatabaseCount("quiz_assignments", 0);
    }

    public function testSkipAlreadyUnassignedUsersWhileUnassigningFromQuiz(): void
    {
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck("id");
        $this->assertDatabaseCount("quiz_assignments", 0);

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/unassign", ["ids" => $userIds]);

        $this->assertDatabaseCount("quiz_assignments", 0);
    }

    public function testSkipEmptyUserListWhileUnassigningFromQuiz(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$this->quiz->id}/invite/unassign", []);

        $this->assertDatabaseCount("quiz_assignments", 0);
    }
}

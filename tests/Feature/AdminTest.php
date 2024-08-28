<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $super_admin;
    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->super_admin = User::factory()->superAdmin()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function testSuperAdminCanViewAdmins(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->super_admin)
            ->get("/admin/admins")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/Index")
                    ->has("users", 1),
            );
    }

    public function testAdminCannotViewAdmins(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->admin)
            ->get("/admin/admins")
            ->assertStatus(403);
    }

    public function testSuperAdminCanViewAddAdminPanel(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->super_admin)
            ->get("/admin/admins/add")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/AddAdmin"),
            );
    }

    public function testAdminCannotViewAddAdminPanel(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->admin)
            ->get("/admin/admins/add")
            ->assertStatus(403);
    }

    public function testSuperAdminCanAddAdmin(): void
    {
        $school = School::factory()->create();
        $this->actingAs($this->super_admin)
            ->post("/admin/admins/add", [
                "name" => "Admin Name",
                "surname" => "Admin Surname",
                "email" => "adminexample@admin.com",
                "password" => "password",
                "school_id" => $school->id,
            ])->assertRedirect("/admin/admins");

        $this->assertDatabaseHas("users", [
            "name" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
        ]);
    }

    public function testAdminCannotAddAdmin(): void
    {
        $school = School::factory()->create();
        $this->actingAs($this->admin)
            ->post("/admin/admins/add", [
                "name" => "Admin Name",
                "surname" => "Admin Surname",
                "email" => "adminexample@admin.com",
                "password" => "password",
                "school_id" => $school->id,
            ])->assertForbidden();

        $this->assertDatabaseMissing("users", [
            "name" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
        ]);
    }

    public function testSuperAdminCanViewEditAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->super_admin)
            ->from("/admin/admins")
            ->get("/admin/admins/{$admin->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/Edit")
                    ->where("user.id", $admin->id),
            );
    }

    public function testAdminCannotViewEditAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->from("/admin/admins")
            ->get("/admin/admins/{$admin->id}")
            ->assertStatus(403);
    }

    public function testSuperAdminCanEditAdmin(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->admin()->create(["school_id" => $school->id]);

        $this->actingAs($this->super_admin)
            ->from("/admin/admins/{$admin->id}")
            ->patch("/admin/admins/{$admin->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $admin->school_id,
            ])
            ->assertRedirect("/admin/admins");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "name" => "New Name",
            "surname" => "New Surname",
            "email" => "new@email.com",
            "school_id" => $admin->school_id,
        ]);
    }

    public function testAdminCannotEditAdmin(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->admin()->create(["school_id" => $school->id]);
        $adminData = $admin->toArray();

        $this->actingAs($this->admin)
            ->from("/admin/admins/{$admin->id}")
            ->patch("/admin/admins/{$admin->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $admin->school_id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas("users", $adminData);
    }

    public function testSuperAdminCannotEditAdminWithInvalidData(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create(["school_id" => $school->id]);

        $response = $this->actingAs($this->super_admin)
            ->from("/admin/admins/{$admin->id}")
            ->patch("/admin/admins/{$admin->id}", [
                "name" => "",
                "surname" => "",
                "email" => "invalidMail",
                "school_id" => 999,
            ]);

        $response->assertRedirect("/admin/admins/{$admin->id}");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "name" => $admin->name,
            "surname" => $admin->surname,
            "email" => $admin->email,
            "school_id" => $admin->school_id,
        ]);

        $response->assertSessionHasErrors([
            "name",
            "surname",
            "email",
            "school_id",
        ]);
    }

    public function testSuperAdminCanDeleteAdmin(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($this->super_admin)
            ->from("/admin/admins")
            ->delete("/admin/admins/{$admin->id}")
            ->assertRedirect("/admin/admins");

        $this->assertDatabaseMissing("users", [
            "id" => $admin->id,
        ]);
    }

    public function testAdminCannotDeleteAdmin(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($this->admin)
            ->from("/admin/admins")
            ->delete("/admin/admins/{$admin->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
        ]);
    }

    public function testUserCannotAccessAdminCrud(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/admins")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/admins/add")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/admins/{$this->admin->id}")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->post("/admin/admins/add", ["name" => "new Name"])
            ->assertForbidden();

        $this->actingAs($user)
            ->from("/dashboard")
            ->patch("/admin/admins/{$this->admin->id}", ["name" => "New Name"])
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->delete("/admin/admins/{$this->admin->id}")
            ->assertStatus(403);
    }

    public function guestCannotAccessAdminCrud(): void
    {
        $user = User::factory()->create();

        $this->from("/dashboard")
            ->get("/admin/admins")
            ->assertStatus(403);

        $this->from("/dashboard")
            ->get("/admin/admins/add")
            ->assertStatus(403);

        $this->from("/dashboard")
            ->get("/admin/admins/{$this->admin->id}")
            ->assertStatus(403);

        $this->from("/dashboard")
            ->post("/admin/admins/add", ["name" => "new Name"])
            ->assertForbidden();

        $this->from("/dashboard")
            ->patch("/admin/admins/{$this->admin->id}", ["name" => "New Name"])
            ->assertStatus(403);

        $this->from("/dashboard")
            ->delete("/admin/admins/{$this->admin->id}")
            ->assertStatus(403);
    }
}

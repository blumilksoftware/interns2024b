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

    protected User $superAdmin;
    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function testSuperAdminCanViewAdmins(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->superAdmin)
            ->get("/admin/admins")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/AdminsPanel")
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

        $this->actingAs($this->superAdmin)
            ->get("/admin/admins/create")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/CreateAdmin"),
            );
    }

    public function testAdminCannotViewAddAdminPanel(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->admin)
            ->get("/admin/admins/create")
            ->assertStatus(403);
    }

    public function testSuperAdminCanAddAdmin(): void
    {
        $school = School::factory()->create();
        $this->actingAs($this->superAdmin)
            ->post("/admin/admins", [
                "firstname" => "Admin Name",
                "surname" => "Admin Surname",
                "email" => "adminexample@admin.com",
                "password" => "password",
                "school_id" => $school->id,
            ])
            ->assertRedirect("/admin/admins");

        $this->assertDatabaseHas("users", [
            "firstname" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
        ]);
    }

    public function testAdminCannotAddAdmin(): void
    {
        $school = School::factory()->create();
        $this->actingAs($this->admin)
            ->post("/admin/admins", [
                "firstname" => "Admin Name",
                "surname" => "Admin Surname",
                "email" => "adminexample@admin.com",
                "password" => "password",
                "school_id" => $school->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing("users", [
            "firstname" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
        ]);
    }

    public function testSuperAdminCanViewEditAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->superAdmin)
            ->from("/admin/admins")
            ->get("/admin/admins/{$admin->id}/edit")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/EditAdmin")
                    ->where("user.id", $admin->id),
            );
    }

    public function testAdminCannotViewEditAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->from("/admin/admins")
            ->get("/admin/admins/{$admin->id}/edit")
            ->assertStatus(403);
    }

    public function testSuperAdminCanEditAdmin(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->admin()->create(["school_id" => $school->id]);

        $this->actingAs($this->superAdmin)
            ->from("/admin/admins/{$admin->id}/edit")
            ->patch("/admin/admins/{$admin->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $admin->school_id,
            ])
            ->assertRedirect("/admin/admins");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "firstname" => "New Name",
            "surname" => "New Surname",
            "email" => "new@email.com",
            "school_id" => $admin->school_id,
        ]);
    }

    public function testAdminCannotEditAdmin(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->admin()->create(["school_id" => $school->id]);

        $this->actingAs($this->admin)
            ->from("/admin/admins/{$admin->id}/edit")
            ->patch("/admin/admins/{$admin->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $admin->school_id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing("users", [
            "firstname" => "New Name",
        ]);
    }

    public function testSuperAdminCannotEditAdminWithInvalidData(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create(["school_id" => $school->id]);

        $response = $this->actingAs($this->superAdmin)
            ->from("/admin/admins/{$admin->id}/edit")
            ->patch("/admin/admins/{$admin->id}", [
                "firstname" => "",
                "surname" => "",
                "email" => "invalidMail",
                "school_id" => 999,
            ]);

        $response->assertRedirect("/admin/admins/{$admin->id}/edit");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "firstname" => $admin->firstname,
            "surname" => $admin->surname,
            "email" => $admin->email,
            "school_id" => $admin->school_id,
        ]);

        $response->assertSessionHasErrors([
            "firstname",
            "surname",
            "email",
            "school_id",
        ]);
    }

    public function testSuperAdminCanDeleteAdmin(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($this->superAdmin)
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
            ->get("/admin/admins/create")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/admins/{$this->admin->id}/edit")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->post("/admin/admins", ["name" => "new Name"])
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
            ->get("/admin/admins/create")
            ->assertStatus(403);

        $this->from("/dashboard")
            ->get("/admin/admins/{$this->admin->id}/edit")
            ->assertStatus(403);

        $this->from("/dashboard")
            ->post("/admin/admins", ["name" => "new Name"])
            ->assertForbidden();

        $this->from("/dashboard")
            ->patch("/admin/admins/{$this->admin->id}", ["name" => "New Name"])
            ->assertStatus(403);

        $this->from("/dashboard")
            ->delete("/admin/admins/{$this->admin->id}")
            ->assertStatus(403);
    }
}

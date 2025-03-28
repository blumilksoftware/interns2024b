<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $superAdmin;
    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school = School::factory()->disabled()->adminSchool()->create();
        $this->superAdmin = User::factory()->superAdmin()->create(["school_id" => $this->school->id]);
        $this->admin = User::factory()->admin()->create(["school_id" => $this->school->id]);
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
                    ->has("users.data", 1),
            );
    }

    public function testAdminCanSearchAdmins(): void
    {
        User::factory()->count(10)->create();
        User::factory(["firstname" => "test"])->admin()->create();
        $this->assertDatabaseCount("users", 13);

        $this->actingAs($this->superAdmin)
            ->get("/admin/admins?search=test")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/AdminsPanel")
                    ->has("users.data", 1),
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

    public function testSuperAdminCanAddAdmin(): void
    {
        $this->actingAs($this->superAdmin)
            ->post("/admin/admins", [
                "firstname" => "Admin Name",
                "surname" => "Admin Surname",
                "email" => "adminexample@admin.com",
                "password" => "password",
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
        $this->actingAs($this->admin)
            ->post("/admin/admins", [
                "firstname" => "Admin Name",
                "surname" => "Admin Surname",
                "email" => "adminexample@admin.com",
                "password" => "password",
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing("users", [
            "firstname" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
        ]);
    }

    public function testSuperAdminCanEditAdmin(): void
    {
        $admin = User::factory()->admin()->create(["school_id" => $this->school->id]);

        $this->actingAs($this->superAdmin)
            ->from("/admin/admins/{$admin->id}/edit")
            ->patch("/admin/admins/{$admin->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "password" => "new @ password",
                "email" => "new@email.com",
            ])
            ->assertRedirect("/admin/admins");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "firstname" => "New Name",
            "surname" => "New Surname",
            "email" => "new@email.com",
        ]);

        $admin->refresh();
        $this->assertTrue(Hash::check("new @ password", $admin->password));
    }

    public function testSuperAdminCanEditAdminWithoutChangingPassword(): void
    {
        $admin = User::factory()->admin()->create(["school_id" => $this->school->id]);
        $oldPassword = $admin->password;

        $this->actingAs($this->superAdmin)
            ->from("/admin/admins/{$admin->id}/edit")
            ->patch("/admin/admins/{$admin->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
            ])
            ->assertRedirect("/admin/admins");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "firstname" => "New Name",
            "surname" => "New Surname",
            "email" => "new@email.com",
            "password" => $oldPassword,
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
                "password" => "asd",
                "email" => "invalidMail",
            ]);

        $response->assertRedirect("/admin/admins/{$admin->id}/edit");

        $this->assertDatabaseHas("users", [
            "id" => $admin->id,
            "firstname" => $admin->firstname,
            "surname" => $admin->surname,
            "email" => $admin->email,
            "password" => $admin->password,
        ]);

        $response->assertSessionHasErrors([
            "firstname",
            "surname",
            "email",
            "password",
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

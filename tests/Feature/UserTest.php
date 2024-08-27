<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function testAdminCanViewUsers(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 11);

        $this->actingAs($this->admin)
            ->get("/admin/users")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/Index")
                    ->has("users", 10),
            );
    }

    public function testAdminCanViewEditUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->from("/admin/users")
            ->get("/admin/users/{$user->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/Edit")
                    ->where("user.id", $user->id),
            );
    }

    public function testAdminCanNotVieEditUserThatDoesNotExist(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/users/999")
            ->assertStatus(404);
    }

    public function testAdminCanEditUser(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(["school_id" => $school->id]);

        $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertRedirect("/admin/users");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "name" => "New Name",
            "surname" => "New Surname",
            "email" => "new@email.com",
            "school_id" => $user->school_id,
        ]);
    }

    public function testAdminCanNotEditUserWithInvalidData(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(["school_id" => $school->id]);

        $response = $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "name" => "",
                "surname" => "",
                "email" => "invalidMail",
                "school_id" => 999,
            ]);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "name" => $user->name,
            "surname" => $user->surname,
            "email" => $user->email,
            "school_id" => $user->school_id,
        ]);

        $response->assertSessionHasErrors([
            "name",
            "surname",
            "email",
            "school_id",
        ]);
    }

    public function testAdminCanNotEditUserMailThatHasBeenTaken(): void
    {
        $school = School::factory()->create();
        User::factory()->create(["email" => "taken@email.com"]);
        $user = User::factory()->create([
            "email" => "old@email.com",
            "school_id" => $school->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "name" => $user->name,
                "surname" => $user->surname,
                "email" => "taken@email.com",
                "school_id" => $user->school_id,
            ]);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertDatabaseHas("users", [
            "email" => "old@email.com",
        ]);

        $response->assertSessionHasErrors([
            "email",
        ]);
    }

    public function testAdminCanNotEditUserThatDoesNotExist(): void
    {
        $this->actingAs($this->admin)
            ->from("/admin/users")
            ->patch("/admin/users/999", ["name" => "New Name"])
            ->assertStatus(404);
    }

    public function testAdminCannotEditAnotherAdmin(): void
    {
        $school = School::factory()->create();
        $anotherAdmin = User::factory()->admin()->create(["school_id" => $school->id]);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$anotherAdmin->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $anotherAdmin->school_id,
            ])
            ->assertForbidden();
    }

    public function testUserCanNotAccessCrud(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/dashboard")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/users/")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/users/{$user->id}")
            ->assertStatus(403);

        $this->actingAs($user)
            ->from("/dashboard")
            ->patch("/admin/users/{$user->id}", ["name" => "New Name"])
            ->assertStatus(403);
    }

    public function guestCanNotAccessCrud(): void
    {
        $user = User::factory()->create();
        $this->from("/")
            ->get("/admin/dashboard")
            ->assertStatus(403)
            ->assertRedirect("/");

        $this->from("/")
            ->get("/admin/users/")
            ->assertStatus(403)
            ->assertRedirect("/");

        $this->from("/")
            ->get("/admin/users/{$user->id}")
            ->assertStatus(403)
            ->assertRedirect("/");

        $this->from("/")
            ->patch("/admin/users/{$user->id}", ["name" => "New Name"])
            ->assertStatus(403)
            ->assertRedirect("/");
    }
}

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

    protected User $superAdmin;
    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function testAdminCanViewUsers(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->admin)
            ->get("/admin/users")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/Index")
                    ->has("users", 10),
            );
    }

    public function testSuperAdminCanViewUsers(): void
    {
        User::factory()->count(10)->create();
        $this->assertDatabaseCount("users", 12);

        $this->actingAs($this->superAdmin)
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

    public function testSuperAdminCanViewEditUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin)
            ->from("/admin/users")
            ->get("/admin/users/{$user->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("User/Edit")
                    ->where("user.id", $user->id),
            );
    }

    public function testAdminCannotViewEditUserThatDoesNotExist(): void
    {
        $this->actingAs($this->admin)
            ->get("/admin/users/999")
            ->assertStatus(404);
    }

    public function testSuperAdminCannotViewEditUserThatDoesNotExist(): void
    {
        $this->actingAs($this->superAdmin)
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

    public function testSuperAdminCanEditUser(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(["school_id" => $school->id]);

        $this->actingAs($this->superAdmin)
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

    public function testAdminCannotEditUserWithInvalidData(): void
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

    public function testAdminCannotEditUserMailThatHasBeenTaken(): void
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

    public function testAdminCannotEditUserThatDoesNotExist(): void
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

    public function testSuperAdminCanEditAnotherAdmin(): void
    {
        $school = School::factory()->create();
        $anotherAdmin = User::factory()->admin()->create(["school_id" => $school->id]);

        $this->actingAs($this->superAdmin)
            ->patch("/admin/users/{$anotherAdmin->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $anotherAdmin->school_id,
            ])
            ->assertRedirect("/admin/users");
    }

    public function testSuperAdminCanAnonymizeUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin)
            ->patch("/admin/users/anonymize/{$user->id}")
            ->assertRedirect()
            ->assertSessionHas("success");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "name" => "Anonymous",
            "surname" => "User",
            "email" => "anonymous" . $user->id . "@email",
            "is_anonymized" => true,
        ]);
    }

    public function testAdminAndSuperAdminCannotViewEditAnonymizedUser(): void
    {
        $user = User::factory()->create(["is_anonymized" => true]);

        $this->actingAs($this->admin)
            ->get("/admin/users/{$user->id}")
            ->assertStatus(403);

        $this->actingAs($this->superAdmin)
            ->get("/admin/users/{$user->id}")
            ->assertStatus(403);
    }

    public function testAdminAndSuperAdminCannotEditAnonymizedUser(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            "school_id" => $school->id,
            "is_anonymized" => true,
        ]);

        $anonymizeUserData = $user->toArray();

        $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas("users", $anonymizeUserData);

        $this->actingAs($this->superAdmin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "name" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas("users", $anonymizeUserData);
    }

    public function testSuperAdminCannotAnonymizeAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/users/anonymize/{$admin->id}")
            ->assertForbidden();
    }

    public function testSuperAdminCannotAnonymizeSuperAdmin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($this->superAdmin)
            ->patch("/admin/users/anonymize/{$superAdmin->id}")
            ->assertStatus(403);
    }

    public function testAdminCannotAnonymizeUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/users/anonymize/{$user->id}")
            ->assertForbidden();
    }

    public function testAdminCannotAnonymizeAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/users/anonymize/{$admin->id}")
            ->assertForbidden();
    }

    public function testUserCannotAccessCrud(): void
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

        $this->actingAs($user)
            ->from("/dashboard")
            ->patch("/admin/users/{$user->id}", ["name" => "New Surname"])
            ->assertStatus(403);
    }

    public function guestCannotAccessCrud(): void
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

        $this->from("/")
            ->patch("/admin/users/{$user->id}", ["name" => "New Surname"])
            ->assertStatus(403)
            ->assertRedirect("/");
    }
}

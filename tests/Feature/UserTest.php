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
                    ->component("Admin/UsersPanel")
                    ->has("users.data", 10),
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
                    ->component("Admin/UsersPanel")
                    ->has("users.data", 10),
            );
    }

    public function testAdminCanViewAnonymousUsers(): void
    {
        User::factory()->count(10)->create();
        User::factory()->anonymized()->count(3)->create();

        $this->assertDatabaseCount("users", 15);

        $this->actingAs($this->superAdmin)
            ->get("/admin/users?anonymized=false")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/UsersPanel")
                    ->has("users.data", 10),
            );

        $this->actingAs($this->superAdmin)
            ->get("/admin/users?anonymized=true")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/UsersPanel")
                    ->has("users.data", 13),
            );
    }

    public function testAdminCanSearchUsers(): void
    {
        User::factory()->count(10)->create();
        User::factory(["firstname" => "test"])->create();

        $this->assertDatabaseCount("users", 13);

        $this->actingAs($this->superAdmin)
            ->get("/admin/users?search=test")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/UsersPanel")
                    ->has("users.data", 1),
            );
    }

    public function testAdminCanEditUser(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(["school_id" => $school->id]);

        $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertRedirect("/admin/users");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => "New Name",
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
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertRedirect("/admin/users");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => "New Name",
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
                "firstname" => "",
                "surname" => "",
                "email" => "invalidMail",
                "school_id" => 999,
            ]);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => $user->firstname,
            "surname" => $user->surname,
            "email" => $user->email,
            "school_id" => $user->school_id,
        ]);

        $response->assertSessionHasErrors([
            "firstname",
            "surname",
            "email",
            "school_id",
        ]);
    }

    public function testAdminCannotEditUserWithDisabledSchool(): void
    {
        $school = School::factory()->create();
        $invalidSchool = School::factory()->disabled()->create();

        $user = User::factory()->create(["school_id" => $school->id]);

        $response = $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "school_id" => $invalidSchool->id,
            ]);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => $user->firstname,
            "surname" => $user->surname,
            "email" => $user->email,
            "school_id" => $user->school_id,
        ]);

        $response->assertSessionHasErrors([
            "school_id",
        ]);
    }

    public function testAdminCannotEditUserWithAdminSchool(): void
    {
        $school = School::factory()->create();
        $invalidSchool = School::factory()->adminSchool()->create();

        $user = User::factory()->create(["school_id" => $school->id]);

        $response = $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "school_id" => $invalidSchool->id,
            ]);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => $user->firstname,
            "surname" => $user->surname,
            "email" => $user->email,
            "school_id" => $user->school_id,
        ]);

        $response->assertSessionHasErrors([
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
                "firstname" => $user->name,
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
                "firstname" => "New Name",
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
                "firstname" => "New Name",
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
            ->patch("/admin/users/{$user->id}/anonymize")
            ->assertRedirect()
            ->assertSessionHas("status", "Dane użytkownika zostały zanonimizowane.");

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => "Anonymous",
            "surname" => "User",
            "email" => "anonymous" . $user->id . "@email",
            "is_anonymized" => true,
        ]);
    }

    public function testAdminAndSuperAdminCannotEditAnonymizedUser(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            "school_id" => $school->id,
            "is_anonymized" => true,
        ]);

        $this->actingAs($this->admin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing("users", [
            "firstname" => "New Name",
        ]);

        $this->actingAs($this->superAdmin)
            ->from("/admin/users/{$user->id}")
            ->patch("/admin/users/{$user->id}", [
                "firstname" => "New Name",
                "surname" => "New Surname",
                "email" => "new@email.com",
                "school_id" => $user->school_id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing("users", [
            "firstname" => "New Name",
        ]);
    }

    public function testSuperAdminCannotAnonymizeAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$admin->id}/anonymize")
            ->assertForbidden();
    }

    public function testSuperAdminCannotAnonymizeSuperAdmin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($this->superAdmin)
            ->patch("/admin/users/{$superAdmin->id}/anonymize")
            ->assertStatus(403);
    }

    public function testAdminCannotAnonymizeUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/anonymize")
            ->assertForbidden();
    }

    public function testAdminCannotAnonymizeAdmin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$admin->id}/anonymize")
            ->assertForbidden();
    }

    public function testUserCannotAccessCrud(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from("/dashboard")
            ->get("/admin/users/")
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

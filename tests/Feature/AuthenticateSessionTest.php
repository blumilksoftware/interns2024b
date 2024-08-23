<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticateSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(["name" => "admin", "guard_name" => "web"]);
        Role::create(["name" => "user", "guard_name" => "web"]);
    }

    public function testUserCanLogin(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);
        $user->assignRole("user");

        $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "goodPassword",
        ])->assertRedirect("/dashboard");
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);
        $user->assignRole("user");

        $this->from("/test")->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "wrongPasswordExample",
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "Wrong email or password"]);
    }

    public function testUserCanNotLoginWithWrongEmail(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);
        $user->assignRole("user");

        $this->from("/test")->post("/auth/login", [
            "email" => "test",
            "password" => "wrongPasswordExample",
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "Pole e-mail nie jest poprawnym adresem e-mail."]);
    }

    public function testUserCanNotLoginWithEmptyEmailAndPassword(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);
        $user->assignRole("user");

        $this->from("/test")->post("/auth/login", [
            "email" => null,
            "password" => null,
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "Pole e-mail jest wymagane.", "password" => "Pole hasło jest wymagane."]);
    }
}

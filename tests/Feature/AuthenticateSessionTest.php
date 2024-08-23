<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateSessionTest extends TestCase
{
    use RefreshDatabase;

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
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "Nieprawidłowy e-mail lub hasło."]);
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

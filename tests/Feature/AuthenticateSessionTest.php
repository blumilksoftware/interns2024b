<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testUserCanLogin(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "goodPassword",
        ])->assertRedirect("/");
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->from('/test')->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "wrongPasswordExample",
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "Wrong email or password"]);
    }

    public function testUserCanNotLoginWithWrongEmail(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->from('/test')->post("/auth/login", [
            "email" => "test",
            "password" => "wrongPasswordExample",
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "The email field must be a valid email address."]);
    }

    public function testUserCanNotLoginWithEmptyEmailAndPassword(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->from('/test')->post("/auth/login", [
            "email" => null,
            "password" => null,
        ])->assertRedirect("/test")->assertSessionHasErrors(["email" => "The email field is required.", "password" => "The password field is required."]);
    }
}

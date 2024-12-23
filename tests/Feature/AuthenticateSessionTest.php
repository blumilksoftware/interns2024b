<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticateSessionTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin(): void
    {
        User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "goodPassword",
        ])->assertRedirect("/");
    }

    public function testRememberCookieIsCreatedWhenUserLoginWithRememberMeOn(): void
    {
        $user = User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $response = $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "goodPassword",
            "remember" => true,
        ]);

        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf("%s|%s|%s", [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
    }

    public function testRememberCookieIsNotCreatedWhenUserLoginWithRememberMeOff(): void
    {
        User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $response = $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "goodPassword",
            "remember" => false,
        ]);

        $response->assertCookieMissing(Auth::guard()->getRecallerName());
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->from("/test")->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "wrongPasswordExample",
        ])
            ->assertRedirect("/test")
            ->assertSessionHasErrors(["email" => "Nieprawidłowy e-mail lub hasło."]);
    }

    public function testUserCanNotLoginWithWrongEmail(): void
    {
        User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->from("/test")->post("/auth/login", [
            "email" => "test",
            "password" => "wrongPasswordExample",
        ])
            ->assertRedirect("/test")
            ->assertSessionHasErrors(["email" => "Pole e-mail nie jest poprawnym adresem e-mail."]);
    }

    public function testUserCanNotLoginWithEmptyEmailAndPassword(): void
    {
        User::factory()->create(["email" => "test@example.com", "password" => "goodPassword"]);

        $this->from("/test")->post("/auth/login", [
            "email" => null,
            "password" => null,
        ])
            ->assertRedirect("/test")
            ->assertSessionHasErrors(["email" => "Pole e-mail jest wymagane.", "password" => "Pole hasło jest wymagane."]);
    }

    public function testUnverifiedUserCanLogin(): void
    {
        User::factory()->unverifiedUser()->create(["email" => "test@example.com", "password" => "goodPassword"]);
        $this->from("/")->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "goodPassword",
        ])
            ->assertRedirect("/");
    }

    public function testUnverifiedUserIsRedirectedToVerifyEmail(): void
    {
        $user = User::factory()->unverifiedUser()->create();
        $this->actingAs($user)
            ->get("/dashboard")
            ->assertRedirect("/email/verify");
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanResetPasswordWithValidToken(): void
    {
        Event::fake();

        $user = User::factory()->create([
            "email" => "test@example.com",
            "password" => Hash::make("oldPassword"),
        ]);

        $token = Password::createToken($user);

        $response = $this->post("/auth/password/reset", [
            "token" => $token,
            "email" => "test@example.com",
            "password" => "newPassword",
            "password_confirmation" => "newPassword",
        ]);

        $response->assertRedirect("/");
        $response->assertSessionHas("status", trans(Password::PASSWORD_RESET));

        $this->assertTrue(Hash::check("newPassword", $user->fresh()->password));

        $loginResponse = $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "newPassword",
        ]);

        $loginResponse->assertRedirect("/");
        $this->assertAuthenticated();
        Event::assertDispatched(PasswordReset::class);
    }

    public function testUserCannotResetPasswordWithInvalidToken(): void
    {
        $user = User::factory()->create([
            "email" => "test@example.com",
            "password" => Hash::make("oldPassword"),
        ]);

        $invalidToken = Str::random(60);

        $response = $this->post("/auth/password/reset", [
            "token" => $invalidToken,
            "email" => "test@example.com",
            "password" => "newPassword",
            "password_confirmation" => "newPassword",
        ]);

        $response->assertRedirect("/");
        $response->assertSessionHasErrors(["email" => trans(Password::INVALID_TOKEN)]);

        $this->assertTrue(Hash::check("oldPassword", $user->fresh()->password));
    }

    public function testUserCannotResetPasswordWithInvalidEmail(): void
    {
        $user = User::factory()->create([
            "email" => "test@example.com",
            "password" => Hash::make("oldPassword"),
        ]);

        $token = Password::createToken($user);

        $response = $this->post("/auth/password/reset", [
            "token" => $token,
            "email" => "wrong@example.com",
            "password" => "newPassword",
            "password_confirmation" => "newPassword",
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(["email" => trans(Password::INVALID_USER)]);

        $this->assertTrue(Hash::check("oldPassword", $user->fresh()->password));
    }

    public function testUserCannotResetPasswordWithMismatchedPasswords(): void
    {
        $user = User::factory()->create([
            "email" => "test@example.com",
            "password" => Hash::make("oldPassword"),
        ]);

        $token = Password::createToken($user);

        $response = $this->post("/auth/password/reset", [
            "token" => $token,
            "email" => "test@example.com",
            "password" => "newPassword",
            "password_confirmation" => "differentPassword",
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(["password" => trans("validation.confirmed", ["attribute" => "hasło"])]);

        $this->assertTrue(Hash::check("oldPassword", $user->fresh()->password));
    }

    public function testAuthUserCanResetPassword(): void
    {
        $user = User::factory()->create([
            "password" => Hash::make("current-password"),
        ]);

        $this->actingAs($user)
            ->from("/profile")
            ->patch(route("profile.password.update"), [
                "current_password" => "current-password",
                "password" => "new-password",
                "password_confirmation" => "new-password",
            ])
            ->assertRedirect("/profile")
            ->assertSessionHas("status", "Zaktualizowano hasło");
        $this->assertTrue(Hash::check("new-password", $user->fresh()->password));
    }

    public function testAuthUserCannotResetPasswordWithWrongPassword(): void
    {
        $user = User::factory()->create([
            "password" => Hash::make("current-password"),
        ]);

        $this->actingAs($user)
            ->from("/profile")
            ->patch(route("profile.password.update"), [
                "current_password" => "wrong-current-password",
                "password" => "new-password",
                "password_confirmation" => "new-password",
            ])
            ->assertRedirect("/profile")
            ->assertSessionHasErrors(["current_password" => trans("validation.current_password", ["attribute" => "current password"])]);
        $this->assertTrue(Hash::check("current-password", $user->fresh()->password));
    }

    public function testAuthUserCannotResetPasswordWithNotMatchingNewPasswords(): void
    {
        $user = User::factory()->create([
            "password" => Hash::make("current-password"),
        ]);

        $this->actingAs($user)
            ->from("/profile")
            ->patch(route("profile.password.update"), [
                "current_password" => "current-password",
                "password" => "new-password",
                "password_confirmation" => "different-password",
            ])
            ->assertRedirect("/profile")
            ->assertSessionHasErrors(["password" => trans("validation.confirmed", ["attribute" => "hasło"])]);
        $this->assertTrue(Hash::check("current-password", $user->fresh()->password));
    }

    public function testUserCannotAccessResetPassword(): void
    {
        $this->from("/")
            ->patch(route("profile.password.update"))
            ->assertRedirect("/auth/login");
    }
}

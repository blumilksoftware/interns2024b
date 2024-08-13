<?php

declare(strict_types=1);

namespace Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use JsonException;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * @throws JsonException
     */
    public function testUserCanSendForgotPasswordRequest(): void
    {
        Notification::fake();
        $user = User::factory()->create(["email" => "test@example.com"]);

        $this->post("/auth/forgot-password", [
            "email" => "test@example.com",
        ])->assertSessionHasNoErrors();

        Notification::assertSentTo(
            [$user],
            ResetPassword::class,
        );
    }

    public function testUserCanNotSendForgotPasswordRequestWithWrongEmail(): void
    {
        Notification::fake();
        $user = User::factory()->create(["email" => "test@example.com"]);

        $this->post("/auth/forgot-password", [
            "email" => "wrongTest@example.com",
        ])->assertSessionHasErrors(["email" => "We can't find a user with that email address."]);
        Notification::assertNothingSent();

        $this->post("/auth/forgot-password", [
            "email" => "wrongTest",
        ])->assertSessionHasErrors(["email" => "The email field must be a valid email address."]);
        Notification::assertNothingSent();

        $this->post("/auth/forgot-password", [
            "email" => null,
        ])->assertSessionHasErrors(["email" => "The email field is required."]);
        Notification::assertNothingSent();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

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
     * @throws JsonException
     */
    public function testUserCanSendForgotPasswordRequest(): void
    {
        Notification::fake();
        $user = User::factory()->create(["email" => "test@gmail.com"]);
        $user->assignRole("user");

        $this->post("/auth/forgot-password", [
            "email" => "test@gmail.com",
        ])->assertSessionHasNoErrors();

        Notification::assertSentTo(
            [$user],
            ResetPassword::class,
        );
    }

    public function testUserCanNotSendForgotPasswordRequestWithWrongEmail(): void
    {
        Notification::fake();
        $user = User::factory()->create(["email" => "test@gmail.com"]);
        $user->assignRole("user");

        $this->post("/auth/forgot-password", [
            "email" => "wrongTest@gmail.com",
        ])->assertSessionHas(["status" => "Przypomnienie hasła zostało wysłane!"]);
        Notification::assertNothingSent();

        $this->post("/auth/forgot-password", [
            "email" => "wrongTest",
        ])->assertSessionHasErrors(["email" => "Pole e-mail nie jest poprawnym adresem e-mail."]);
        Notification::assertNothingSent();

        $this->post("/auth/forgot-password", [
            "email" => null,
        ])->assertSessionHasErrors(["email" => "Pole e-mail jest wymagane."]);
        Notification::assertNothingSent();
    }
}
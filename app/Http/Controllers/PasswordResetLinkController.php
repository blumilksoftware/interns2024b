<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render("Auth/ForgotPassword", ["status" => session()->get("status")]);
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $request->only("email");

        $status = Password::sendResetLink(
            $request->only("email"),
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(["status" => __($status)])
            : back()->withErrors(["email" => __("passwords.sent")]);
    }

    public function resetCreate(string $token): Response
    {
        return Inertia::render("Auth/ResetPassword", ["token" => $token]);
    }

    public function resetStore(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function (User $user, string $password): void {
                $user->forceFill([
                    "password" => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            },
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route("home")->with("status", __($status))
            : back()->withErrors(["email" => [__($status)]]);
    }
}

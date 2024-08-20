<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileUserController extends Controller
{
    public function create(): Response
    {
        $user = auth()->user();

        return Inertia::render(
            "Auth/Profile",
            [
                "user" => $user,
                "status" => session("status"),
            ],
        );
    }

    public function forgotPassword(): RedirectResponse
    {
        $userEmail = auth()->user()->email;
        $request = ForgotPasswordRequest::create("/forgot-password", "POST", ["email" => $userEmail]);
        $passwordResetLinkController = new PasswordResetLinkController();

        $response = $passwordResetLinkController->store($request);

        return redirect()->route("profile")->with("status", session("status"));
    }
}

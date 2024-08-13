<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render("Auth/Forgot-Password");
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            "email" => "required|string|email",
        ]);

        $status = Password::sendResetLink(
            $request->only("email"),
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(["status" => __($status)])
            : back()->withErrors(["email" => __($status)]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileUserController extends Controller
{
    public function create(Request $request): Response
    {
        $user = $request->user()->load("school");

        return Inertia::render(
            "Auth/Profile",
            [
                "user" => UserResource::make($user),
                "status" => session("status"),
            ],
        );
    }

    public function edit(Request $request): Response
    {
        return Inertia::render("Auth/PasswordUpdate");
    }

    public function update(Request $request, Hasher $hasher): RedirectResponse
    {
        $validated = $request->validate([
            "current_password" => ["required", "current_password"],
            "password" => ["required", Password::defaults(), "confirmed"],
        ]);

        $user = $request->user();
        $user->password = $hasher->make($validated["password"]);
        $user->save();

        return redirect()->back()
            ->with("success", "Zaktualizowano hasło");
    }
}

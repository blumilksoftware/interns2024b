<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ProfileUserPasswordResetRequest;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileUserController extends Controller
{
    public function create(Request $request): Response
    {
        $user = $request->user()->load("school");

        return Inertia::render(
            "User/Profile",
            [
                "user" => UserResource::make($user),
            ],
        );
    }

    public function update(ProfileUserPasswordResetRequest $request, Hasher $hasher): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $user->password = $hasher->make($validated["password"]);
        $user->save();

        event(new PasswordReset($user));

        return redirect()->back()
            ->with("status", "Zaktualizowano hasło");
    }
}

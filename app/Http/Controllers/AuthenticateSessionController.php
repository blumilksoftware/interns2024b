<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthenticateSessionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class AuthenticateSessionController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function authenticate(AuthenticateSessionRequest $request): RedirectResponse
    {
        $credentials = $request->only("email", "password");

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            if ($request->user()->isAdmin()) {
                return Redirect::route("admin.dashboard")->with("success", "Welcome to the Admin Dashboard");
            }

            return Redirect::route("dashboard")->with("success");
        }

        throw ValidationException::withMessages([
            "email" => "Wrong email or password",
        ]);
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return Redirect::route("home")->with("success");
    }
}

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

            return $request->user()->hasRole(["admin", "super-admin"])
                ? Redirect::route("admin.dashboard")->with("success", "Welcome to the Admin Dashboard")
                : Redirect::route("dashboard")->with("success");
        }

        throw ValidationException::withMessages([
            "email" => "Nieprawidłowy e-mail lub hasło.",
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticateSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render("Auth/Login");
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required|string",
        ]);

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            return Redirect::route("home")->with("success");
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

        return Redirect::route("home")->with("succes");
    }
}

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
        $remember = $request->input("remember", false);

        if (auth()->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->route("home");
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

        return Redirect::route("home")
            ->with("success");
    }
}

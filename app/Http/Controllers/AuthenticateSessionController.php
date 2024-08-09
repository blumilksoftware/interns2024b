<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticateSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render("Auth/Login");
    }

    public function authenticate(Request $request): RedirectResponse
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);


        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return Redirect::route("home")->with('succes');
        }

        return back()->withErrors([
            'The provided mail or password is invalid, try again.',
        ]);
    }
}

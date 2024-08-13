<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Mail\RegistrationMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RegisterUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render("Auth/Register");
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = new User($request->validated());
        $user->password = Hash::make($request->getPassword());
        $user->save();

        event(new Registered($user));
        Mail::to($user->email)->send(new RegistrationMail());
        return Redirect::route("home");
    }
}
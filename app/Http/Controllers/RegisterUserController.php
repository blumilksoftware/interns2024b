<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class RegisterUserController extends Controller
{
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $userExists = User::query()->where("email", $request->email)->exists();
        $user = new User($request->validated());
        $user->password = Hash::make($request->password);

        if (!$userExists) {
            $user->save();
            event(new Registered($user));
            Auth::login($user);
        }

        return Redirect::route("home");
    }
}

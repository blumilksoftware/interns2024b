<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerifyController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return Redirect::route("home");
    }

    public function create(): Response
    {
        return Inertia::render("Auth/VerifyEmail");
    }

    public function send(Request $request): RedirectResponse
    {
        if (auth()->check()) {
            $request->user()->sendEmailVerificationNotification();

            return back()
                ->with("status", "Wiadomość z linkiem aktywacyjnym została wysłana na Twój adres e-mail!");
        }

        return back()
            ->with("status", "Wiadomość z linkiem aktywacyjnym została wysłana na Twój adres e-mail!");
    }
}

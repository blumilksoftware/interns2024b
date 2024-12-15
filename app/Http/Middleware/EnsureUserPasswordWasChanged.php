<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserPasswordWasChanged
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->force_password_change) {
            return redirect(route("profile"))
                ->with("status", "Proszę zmienić hasło.");
        }

        return $next($request);
    }
}

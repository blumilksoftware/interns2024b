<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role === Role::USER) {
            abort(403);
        }

        return $next($request);
    }
}

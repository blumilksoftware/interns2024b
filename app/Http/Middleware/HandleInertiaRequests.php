<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /** @var string */
    protected $rootView = "app";

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            "appName" => fn() => config("app.name"),
            "user" => fn() => $request->user() ? UserResource::make($request->user()) : null,
            "flash" => $this->getFlashData($request),
        ]);
    }

    protected function getFlashData(Request $request): Closure
    {
        return fn(): array => [
            "errors" => $request->session()->get("errors"),
            "status" => $request->session()->get("status"),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $isSuperAdmin = $request->user()->hasRole("super_admin");

        return Inertia::render("Auth/Admin/Dashboard", [
            "isSuperAdmin" => $isSuperAdmin,
        ]);
    }
}

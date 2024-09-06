<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()->role("user")->with("school")->orderBy("id")->get();

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($users),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()->role("user")->with(["school"])->orderBy("id")->get();

        return Inertia::render("User/Index", [
            "users" => UserResource::collection($users),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Quiz;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    public function index(Quiz $quiz): Response
    {
        $users = User::query()->role("user")->with("school")->orderBy("id");

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($users->paginate(100)),
            "quiz" => $quiz,
        ]);
    }
}

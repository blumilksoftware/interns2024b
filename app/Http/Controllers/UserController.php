<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

    public function edit(User $user): Response
    {
        return Inertia::render("User/Edit", [
            "user" => new UserResource($user),
            "schools" => School::all(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $user->update($data);

        return redirect()->route("admin.users.index")->with("success", "User updated successfully");
    }
}

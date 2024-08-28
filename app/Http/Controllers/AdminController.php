<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        $users = User::query()->role("admin")->with(["school"])->orderBy("id")->get();

        return Inertia::render("Admin/Index", [
            "users" => UserResource::collection($users),
        ]);
    }

    public function addPanel(): Response
    {
        return Inertia::render("Admin/AddAdmin");
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $userExists = User::query()->where("email", $request->email)->exists();

        if (!$userExists) {
            $user = new User($request->validated());
            $user->password = Hash::make($request->password);
            $user->save();
            $user->syncRoles("admin");
            event(new Registered($user));
        }

        return redirect()->route("admin.admins.index")->with("success", "Administrator został utworzony pomyślnie.");
    }

    public function edit(User $user): Response
    {
        $this->authorize("update", $user);

        return Inertia::render("Admin/Edit", [
            "user" => new UserResource($user),
            "schools" => School::all(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize("update", $user);
        $data = $request->validated();
        $user->update($data);

        return redirect()->route("admin.admins.index")->with("success", "Administrator zaktualizowany pomyślnie.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->back();
    }
}

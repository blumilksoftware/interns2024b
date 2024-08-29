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
        $isSuperAdmin = auth()->user()->hasRole("super_admin");
        $users = User::query()->role("user")->with(["school"])->orderBy("id")->get();

        return Inertia::render("User/Index", [
            "users" => UserResource::collection($users),
            "isSuperAdmin" => $isSuperAdmin,
        ]);
    }

    public function edit(User $user): Response
    {
        $this->authorize("update", $user);

        return Inertia::render("User/Edit", [
            "user" => new UserResource($user),
            "schools" => School::all(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize("update", $user);
        $data = $request->validated();
        $user->update($data);

        return redirect()
            ->route("admin.users.index")
            ->with("success", "Użytkownik zaktualizowany pomyślnie.");
    }

    public function anonymize(User $user): RedirectResponse
    {
        if ($user->hasRole("user")) {
            $this->authorize("update", $user);
            $user->update([
                "name" => "Anonymous",
                "surname" => "User",
                "email" => "anonymous" . $user->id . "@email",
                "is_anonymized" => true,
            ]);

            return redirect()
                ->back()
                ->with("success", "Dane użytkownika zostały zanonimizowane.");
        }

        return redirect()
            ->back()
            ->with("error", "Nie można zanonimizować tego użytkownika.");
    }
}

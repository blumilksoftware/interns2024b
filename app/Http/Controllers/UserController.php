<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\SortHelper;
use App\Http\Requests\UserRequest;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\UserResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(SortHelper $sorter): Response
    {
        $users = User::query()->role("user")->with("school");
        $schools = School::query()->orderBy("id")->get();

        $query = $sorter->sort($users, ["id", "email", "updated_at", "created_at"], ["name", "school"]);
        $query = $this->sortBySchool($query, $sorter);
        $query = $this->sortByName($query, $sorter);
        $query = $sorter->search($query, "name");

        return Inertia::render("Admin/UsersPanel", [
            "users" => UserResource::collection($query->paginate()),
            "schools" => SchoolResource::collection($schools),
        ]);
    }

    public function edit(User $user): Response
    {
        $this->authorize("update", $user);

        return Inertia::render("Admin/EditUser", [
            "user" => UserResource::make($user),
            "schools" => SchoolResource::collection(School::all()),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize("update", $user);
        $data = $request->validated();
        $user->update($data);

        return redirect()
            ->route("admin.users.index")
            ->with("status", "Użytkownik zaktualizowany pomyślnie.");
    }

    public function anonymize(User $user): RedirectResponse
    {
        $this->authorize("anonymize", $user);
        $user->update([
            "firstname" => "Anonymous",
            "surname" => "User",
            "email" => "anonymous" . $user->id . "@email",
            "is_anonymized" => true,
        ]);

        return redirect()
            ->back()
            ->with("status", "Dane użytkownika zostały zanonimizowane.");
    }

    private function sortByName(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "name") {
            return $query->orderBy("surname", $order)
                ->orderBy("firstname", $order);
        }

        return $query;
    }

    private function sortBySchool(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "school") {
            return $query->orderBy("school.name", $order);
        }

        return $query;
    }
}

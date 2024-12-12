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
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(SortHelper $sorter, Request $request): Response
    {
        $users = User::query()->role("user")->with("school");
        $schools = School::query()
            ->where("is_admin_school", false)
            ->orderBy("name")
            ->get();

        $query = $sorter->sort($users, ["id", "email", "updated_at", "created_at"], ["firstname", "school"]);
        $query = $this->filterAnonymizedUsers($query, $request);
        $query = $this->sortBySchool($query, $sorter);
        $query = $this->sortByName($query, $sorter);
        $query = $sorter->search($query, "firstname");

        return Inertia::render("Admin/UsersPanel", [
            "users" => UserResource::collection($query->paginate()),
            "schools" => SchoolResource::collection($schools),
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

        if ($field === "firstname") {
            return $query->orderBy("firstname", $order)
                ->orderBy("surname", $order);
        }

        return $query;
    }

    private function sortBySchool(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "school") {
            return $query->join("schools", "users.school_id", "=", "schools.id")
                ->orderBy("schools.city", $order)
                ->orderBy("schools.name", $order)
                ->select("users.*");
        }

        return $query;
    }

    private function filterAnonymizedUsers(Builder $query, Request $request): Builder
    {
        $showAnonymized = $request->query("anonymized", "false") === "true";

        if (!$showAnonymized) {
            return $query->where("is_anonymized", false);
        }

        return $query;
    }
}

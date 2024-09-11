<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\InviteQuizRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendInviteJob;
use App\Models\Quiz;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    public function index(Quiz $quiz, Request $request): Response
    {
        $this->authorize("invite", $quiz);

        $searchText = $request->query("search");
        $sortField = $request->query("sort", "id");
        $sortDirection = $request->query("order", "asc");
        $schoolId = $request->query("schoolId") !== null ? (int)$request->query("schoolId") : null;
        $limit = $request->query("limit");

        $users = User::query()->role("user")->with("school")->whereNotNull("email_verified_at");

        $schools = School::all(["id", "name", "city"]);

        $this->applyFilters($users, $searchText, $schoolId);
        $this->applySorting($users, $sortField, $sortDirection);

        if ($limit && $limit > 0) {
            $users = $users->paginate((int)$limit);
        } else {
            $users = $users->paginate(100);
        }

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($users),
            "quiz" => $quiz,
            "schools" => $schools,
            "filters" => [
                "search" => $searchText,
                "sort" => $sortField,
                "order" => $sortDirection,
                "schoolId" => $schoolId,
                "limit" => $limit,
            ],
        ]);
    }

    public function store(Quiz $quiz, InviteQuizRequest $request): RedirectResponse
    {
        $this->authorize("invite", $quiz);
        $userIds = $request->input("ids", []);

        if (empty($userIds)) {
            return redirect()->back()->withErrors("Brak użytkowników do zaproszenia.");
        }

        $users = User::query()->whereIn("id", $userIds)->get();

        $quiz->assignedUsers()->attach($userIds);

        foreach ($users as $user) {
            SendInviteJob::dispatch($user, $quiz)->delay(now()->addMinutes(5));
        }

        return redirect()
            ->back()
            ->with("status", "Zaproszenia zostały zaplanowane do wysłania.");
    }

    private function applySorting($query, string $sortField, string $sortDirection): void
    {
        $allowedFields = ["id", "name", "surname", "school"];
        $allowedDirections = ["asc", "desc"];

        if (!in_array($sortField, $allowedFields, true)) {
            $sortField = "id";
        }

        if (!in_array($sortDirection, $allowedDirections, true)) {
            $sortDirection = "asc";
        }

        switch ($sortField) {
            case "school":
                $query->join("schools", "users.school_id", "=", "schools.id")
                    ->orderBy("schools.name", $sortDirection)
                    ->select("users.*");

                break;
            default:
                $query->orderBy($sortField, $sortDirection);

                break;
        }
    }

    private function applyFilters($query, ?string $searchText, ?int $schoolId): void
    {
        if ($schoolId) {
            $query->where("school_id", $schoolId);
        }

        if ($searchText) {
            $query->where(function ($query) use ($searchText): void {
                $query->where("users.name", "like", "%$searchText%")
                    ->orWhere("users.surname", "like", "%$searchText%");
            });
        }
    }
}

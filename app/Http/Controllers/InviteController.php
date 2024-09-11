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
        $sort = $request->query("sort", "id");
        $schoolId = $request->query("schoolId");
        $users = User::query()->role("user")->with("school");

        if ($schoolId) {
            $users->where("school_id", $schoolId);
        }

        if ($sort === "id") {
            $users->orderBy("id", "asc");
        } elseif ($sort === "name-asc") {
            $users->orderBy("name", "asc");
        } elseif ($sort === "name-desc") {
            $users->orderBy("name", "desc");
        } elseif ($sort === "surname-asc") {
            $users->orderBy("surname", "asc");
        } elseif ($sort === "surname-desc") {
            $users->orderBy("surname", "desc");
        } elseif ($sort === "school-asc") {
            $users->join("schools", "users.school_id", "=", "schools.id")
                ->orderBy("schools.name", "asc")
                ->select("users.*");
        } elseif ($sort === "school-desc") {
            $users->join("schools", "users.school_id", "=", "schools.id")
                ->orderBy("schools.name", "desc")
                ->select("users.*");
        } else {
            $users->orderBy("id");
        }

        if ($searchText) {
            $users->where(function ($query) use ($searchText): void {
                $query->where("users.name", "like", "%$searchText%")
                    ->orWhere("users.surname", "like", "%$searchText%");
            });
        }

        $schools = School::all(["id", "name", "city"]);

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($users->paginate(100)),
            "quiz" => $quiz,
            "schools" => $schools,
            "filters" => [
                "search" => $searchText,
                "sort" => $sort,
                "schoolId" => $schoolId,
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

        foreach ($users as $user) {
            SendInviteJob::dispatch($user, $quiz)->delay(now()->addMinutes(5));
        }

        return redirect()
            ->back()
            ->with("status", "Zaproszenia zostały zaplanowane do wysłania.");
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AssignToQuizAction;
use App\Actions\UnassignToQuizAction;
use App\Helpers\SortHelper;
use App\Http\Requests\InviteQuizRequest;
use App\Http\Resources\QuizResource;
use App\Http\Resources\UserResource;
use App\Models\Quiz;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    public function index(Quiz $quiz, SortHelper $sort, Request $request): Response
    {
        $this->authorize("invite", $quiz); // TODO
        $query = User::query()->role("user")->with("school")->whereNotNull("email_verified_at");
        $query = $sort->sort($query, ["id"], ["name", "school"]);
        $query = $this->sortByName($query, $sort);
        $query = $this->sortBySchool($query, $sort);
        $query = $this->filterByName($query, $request);
        $query = $this->filterBySchool($query, $request);
        $schools = School::all(["id", "name", "city"]); // TODO resource

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($sort->paginate($query)),
            "quiz" => QuizResource::make($quiz),
            "schools" => $schools,
            "assigned" => $quiz->assignedUsers->pluck("id"),
        ]);
    }

    public function assign(Quiz $quiz, InviteQuizRequest $request, AssignToQuizAction $assignAction): RedirectResponse
    {
        $this->authorize("invite", $quiz);

        $assignAction->execute($quiz, collect($request->input("ids")));

        return redirect()
            ->back()
            ->with("status", "Użytkownicy zostali przypisani do quizu.");
    }

    public function unassign(Quiz $quiz, InviteQuizRequest $request, UnassignToQuizAction $unassignAction): RedirectResponse
    {
        $this->authorize("invite", $quiz);

        $unassignAction->execute($quiz, collect($request->input("ids")));

        return redirect()
            ->back()
            ->with("status", "Użytkownicy zostali wypisani z quizu.");
    }

    private function filterByName(Builder $query, Request $request): Builder
    {
        $searchName = $request->query("search");

        if ($searchName) {
            return $query->where("users.name", "ilike", "%$searchName%")
                ->orWhere("users.surname", "ilike", "%$searchName%");
        }

        return $query;
    }

    private function filterBySchool(Builder $query, Request $request): Builder
    {
        $searchSchool = $request->query("schoolId");

        if ($searchSchool) {
            return $query->where("school_id", $searchSchool);
        }

        return $query;
    }

    private function sortByName(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "name") {
            return $query->orderBy("surname", $order)
                ->orderBy("name", $order);
        }

        return $query;
    }

    private function sortBySchool(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "schoolId") {
            return $query->orderBy("school.name", $order);
        }

        return $query;
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AssignToQuizAction;
use App\Actions\UnassignFromQuizAction;
use App\Helpers\SortHelper;
use App\Http\Requests\InviteQuizRequest;
use App\Http\Resources\UserResource;
use App\Models\Quiz;
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
        $this->authorize("invite", $quiz);
        $query = User::query()->role("user")->with("school")->whereNotNull("email_verified_at");

        $query = $sort->sort($query, ["id"], ["name", "school"]);
        $query = $sort->sort($query, ["id"], ["name", "school"]);
        $query = $this->sortByName($query, $sort);
        $query = $this->sortBySchool($query, $sort);
        $query = $this->filterByMode($query, $request);
        $query = $this->filterBySchool($query, $request);

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($sort->paginate($query)),
            "quiz" => $quiz->id,
            "assigned" => $quiz->assignedUsers->pluck("id"),
        ]);
    }

    public function assign(Quiz $quiz, InviteQuizRequest $request, AssignToQuizAction $assignAction): RedirectResponse
    {
        $this->authorize("invite", $quiz);

        $assignAction->execute($quiz, collect($request->input("ids")));

        return redirect()
            ->back()
            ->with("status", "Użytkownicy zostali przypisani do testu.");
    }

    public function unassign(Quiz $quiz, InviteQuizRequest $request, UnassignFromQuizAction $unassignAction): RedirectResponse
    {
        $this->authorize("invite", $quiz);

        $unassignAction->execute($quiz, collect($request->input("ids")));

        return redirect()
            ->back()
            ->with("status", "Użytkownicy zostali wypisani z testu.");
    }

    private function filterByMode(Builder $query, Request $request)
    {
        $mode = $request->query("mode", "user");

        if ($mode === "school") {
            return $this->filterBySchoolName($query, $request);
        }

        return $this->filterByName($query, $request);
    }

    private function filterByName(Builder $query, Request $request): Builder
    {
        $searchName = $request->query("search");

        if ($searchName) {
            return $query->where("users.firstname", "ilike", "%$searchName%")
                ->orWhere("users.surname", "ilike", "%$searchName%");
        }

        return $query;
    }

    private function filterBySchoolName(Builder $query, Request $request): Builder
    {
        $searchName = $request->query("search");

        if ($searchName) {
            return $query->whereHas("school", function (Builder $schoolQuery) use ($searchName): void {
                $schoolQuery->where("name", "ilike", "%$searchName%");
            });
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
                ->orderBy("firstname", $order);
        }

        return $query;
    }

    private function sortBySchool(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "school") {
            return $query
                ->leftJoin("schools", "users.school_id", "=", "schools.id")
                ->orderBy("schools.name", $order)
                ->select("users.*");
        }

        return $query;
    }
}

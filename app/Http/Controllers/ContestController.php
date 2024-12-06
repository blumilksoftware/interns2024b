<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\SortHelper;
use App\Http\Resources\QuizResource;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\UserQuizResource;
use App\Models\Quiz;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContestController extends Controller
{
    public function index(SortHelper $sorter): Response
    {
        $query = $sorter->sort(School::query(), ["id", "name", "regon", "updated_at", "created_at"], ["students", "address"]);
        $query = $sorter->search($query, "name");
        $schools = $sorter->paginate($query);

        return Inertia::render("Home", ["schools" => SchoolResource::collection($schools)]);
    }

    public function create(Request $request): RedirectResponse|Response
    {
        $user = $request->user();

        if ($user->hasRole(["admin", "super_admin"])) {
            return redirect()->route("admin.quizzes.index");
        }

        $userQuizzes = $user->userQuizzes()
            ->with(["userQuestions.question.answers", "quiz"])
            ->get();

        $quizzes = Quiz::query()
            ->whereNotNull("locked_at")
            ->with("questions.answers")
            ->get();

        return Inertia::render("User/Dashboard", [
            "userQuizzes" => UserQuizResource::collection($userQuizzes),
            "quizzes" => QuizResource::collection($quizzes),
        ]);
    }
}

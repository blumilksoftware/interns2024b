<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\SortHelper;
use App\Http\Resources\QuizResource;
use App\Http\Resources\UserQuizResource;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContestController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("Home");
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

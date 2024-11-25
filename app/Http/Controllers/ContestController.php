<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\UserQuizResource;
use App\Models\Quiz;
use App\Models\School;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContestController extends Controller
{
    public function index(): Response
    {
        $schools = School::all()->sortBy("name");

        return Inertia::render("Home", ["schools" => SchoolResource::collection($schools)]);
    }

    public function create(Request $request): Response
    {
        $user = $request->user();
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

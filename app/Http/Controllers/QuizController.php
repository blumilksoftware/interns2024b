<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware("can:update,quiz", only: ["update"]),
            new Middleware("can:delete,quiz", only: ["destroy"]),
        ];
    }

    public function index(): Response
    {
        $quizzes = Quiz::query()
            ->with("questions.answers")
            ->get();

        return Inertia::render("Quiz/Index", ["quizzes" => QuizResource::collection($quizzes)]);
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        Quiz::query()->create($request->validated());

        return redirect()
            ->back()
            ->with("success", "Quiz added successfully");
    }

    public function show(int $quiz): Response
    {
        $quiz = Quiz::query()
            ->with("questions.answers")
            ->findOrFail($quiz);

        return Inertia::render("Quiz/Show", ["quiz" => new QuizResource($quiz)]);
    }

    public function update(QuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($request->validated());

        return redirect()
            ->back()
            ->with("success", "Quiz updated");
    }

    public function lock(Quiz $quiz): RedirectResponse
    {
        $quiz->locked_at = Carbon::now();
        $quiz->save();

        return redirect()
            ->back()
            ->with("success", "Quiz locked");
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()
            ->back()
            ->with("success", "Quiz deleted");
    }

    public function clone(Quiz $quiz): RedirectResponse
    {
        $quiz->clone();

        return redirect()
            ->back()
            ->with("success", "Quiz cloned");
    }
}

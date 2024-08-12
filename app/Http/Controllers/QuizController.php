<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
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

    /**
     * @throws AuthorizationException
     */
    public function update(QuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $this->authorize('modify', $quiz);

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

    /**
     * @throws AuthorizationException
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $this->authorize('destroy', $quiz);

        $quiz->delete();

        return redirect()
            ->back()
            ->with("success", "Quiz deleted");
    }
}

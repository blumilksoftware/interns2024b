<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function index(): Response
    {
        $quizzes = Quiz::query()
            ->with("questions.answers")
            ->get();

        return Inertia::render("Admin/Quizzes", ["quizzes" => QuizResource::collection($quizzes)]);
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        Quiz::query()->create($request->validated());

        return redirect()->back();
    }

    public function update(QuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->back();
    }

    public function clone(Quiz $quiz): RedirectResponse
    {
        $quiz->clone();

        return redirect()
            ->back()
            ->with("success", "Test został skopiowany");
    }

    public function createSubmission(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = $request->user();
        $submission = $quiz->createSubmission($user);

        return redirect("/submissions/{$submission->id}/");
    }
}

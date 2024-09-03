<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Services\QuizUpdateService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function redirect;

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

    public function update(QuizUpdateService $service, UpdateQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $service->update($quiz, $request->validated());

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
            ->with("status", "Test został skopiowany");
    }

    public function lock(Quiz $quiz): RedirectResponse
    {
        $quiz->locked_at = Carbon::now();
        $quiz->save();

        return redirect()
            ->back()
            ->with("status", "Test oznaczony jako gotowy do publikacji");
    }

    public function unlock(Quiz $quiz): RedirectResponse
    {
        $quiz->locked_at = null;
        $quiz->save();

        return redirect()
            ->back()
            ->with("status", "Publikacja testu została wycofana");
    }

    public function createSubmission(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = $request->user();
        $submission = $quiz->createSubmission($user);

        return redirect("/submissions/{$submission->id}/");
    }

    public function assign(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        $quiz->assignedUsers()->attach($user);
        $quiz->save();

        return redirect()
            ->back()
            ->with("status", "Przypisano do testu");
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class QuizQuestionController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware("can:create," . Question::class . ",quiz", only: ["store"]),
            new Middleware("can:clone,question,quiz", only: ["clone"]),
            new Middleware("can:update,question", only: ["update"]),
            new Middleware("can:delete,question", only: ["destroy"]),
        ];
    }

    public function index(Quiz $quiz): Response
    {
        $questions = $quiz->questions()
            ->with("answers")
            ->get();

        return Inertia::render("Question/Index", [
            "questions" => QuestionResource::collection($questions),
        ]);
    }

    public function store(Quiz $quiz, QuestionRequest $request): RedirectResponse
    {
        Question::query()
            ->make($request->validated())
            ->quiz()->associate($quiz)
            ->save();

        return redirect()
            ->back()
            ->with("success", "Question added successfully");
    }

    public function show(int $question): Response
    {
        $test = Question::query()
            ->with("answers")
            ->findOrFail($question);

        return Inertia::render("Question/Show", ["question" => new QuestionResource($test)]);
    }

    public function update(QuestionRequest $request, Question $question): RedirectResponse
    {
        $question->update($request->validated());

        return redirect()
            ->back()
            ->with("success", "Question updated");
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()
            ->back()
            ->with("success", "Question deleted");
    }

    public function clone(Question $question, Quiz $quiz): RedirectResponse
    {
        $question->cloneTo($quiz);

        return redirect()
            ->back()
            ->with("success", "Question cloned");
    }
}

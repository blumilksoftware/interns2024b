<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuizQuestionController extends Controller
{
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
            ->create($request->validated())
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
}

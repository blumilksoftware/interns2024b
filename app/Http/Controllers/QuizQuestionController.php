<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * @throws AuthorizationException
     */
    public function store(Quiz $quiz, QuestionRequest $request): RedirectResponse
    {
        if ($quiz->isLocked) {
            throw new AuthorizationException();
        }

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

    /**
     * @throws AuthorizationException
     */
    public function update(QuestionRequest $request, Question $question): RedirectResponse
    {
        $this->authorize("modify", $question);

        $question->update($request->validated());

        return redirect()
            ->back()
            ->with("success", "Question updated");
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Question $question): RedirectResponse
    {
        $this->authorize("destroy", $question);

        $question->delete();

        return redirect()
            ->back()
            ->with("success", "Question deleted");
    }

    /**
     * @throws AuthorizationException
     */
    public function clone(Question $question, Quiz $quiz): RedirectResponse
    {
        $question->cloneTo($quiz);

        return redirect()
            ->back()
            ->with("success", "Question cloned");
    }
}

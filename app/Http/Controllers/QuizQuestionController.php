<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;

class QuizQuestionController extends Controller
{
    public function store(Quiz $quiz, QuestionRequest $request): RedirectResponse
    {
        Question::query()
            ->make($request->validated())
            ->quiz()->associate($quiz)
            ->save();

        return redirect()->back();
    }

    public function update(QuestionRequest $request, Question $question): RedirectResponse
    {
        $question->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()->back();
    }

    public function clone(Question $question, Quiz $quiz): RedirectResponse
    {
        $question->cloneTo($quiz);

        return redirect()
            ->back()
            ->with("success", "Pytanie zostało skopiowane");
    }
}

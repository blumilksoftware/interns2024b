<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;

class QuestionAnswerController extends Controller
{
    public function store(Question $question, AnswerRequest $request): RedirectResponse
    {
        Answer::query()
            ->make($request->validated())
            ->question()->associate($question)
            ->save();

        return redirect()->back();
    }

    public function markAsCorrect(Answer $answer): RedirectResponse
    {
        $answer->question->correctAnswer()->associate($answer)->save();

        return redirect()->back();
    }

    public function markAsInvalid(Answer $answer): RedirectResponse
    {
        if ($answer->isCorrect) {
            $answer->question->correct_answer_id = null;
            $answer->save();
        }

        return redirect()->back();
    }

    public function update(AnswerRequest $request, Answer $answer): RedirectResponse
    {
        $answer->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Answer $answer): RedirectResponse
    {
        $answer->delete();

        return redirect()->back();
    }

    public function clone(Answer $answer, Question $question): RedirectResponse
    {
        $answer->cloneTo($question);

        return redirect()
            ->back()
            ->with("success", "Odpowiedź została skopiowana");
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class QuestionAnswerController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware("can:create," . Answer::class . ",question", only: ["store"]),
            new Middleware("can:clone,answer,question", only: ["clone"]),
            new Middleware("can:update,answer", only: ["update", "markAsCorrect", "markAsInvalid"]),
            new Middleware("can:delete,answer", only: ["destroy"]),
        ];
    }

    public function index(Question $question): Response
    {
        return Inertia::render("Answer/Index", [
            "answers" => AnswerResource::collection($question->answers),
        ]);
    }

    public function store(Question $question, AnswerRequest $request): RedirectResponse
    {
        Answer::query()
            ->make($request->validated())
            ->question()->associate($question)
            ->save();

        return redirect()
            ->back()
            ->with("success", "Answer added successfully");
    }

    public function show(Answer $answer): Response
    {
        return Inertia::render("Answer/Show", ["answer" => new AnswerResource($answer)]);
    }

    public function markAsCorrect(Answer $answer): RedirectResponse
    {
        $answer->question->correctAnswer()->associate($answer)->save();

        return redirect()
            ->back()
            ->with("success", "Answer marked as correct");
    }

    public function markAsInvalid(Answer $answer): RedirectResponse
    {
        if ($answer->isCorrect) {
            $answer->question->correct_answer_id = null;
            $answer->save();
        }

        return redirect()
            ->back()
            ->with("success", "Answer marked as incorrect");
    }

    public function update(AnswerRequest $request, Answer $answer): RedirectResponse
    {
        $answer->update($request->validated());

        return redirect()
            ->back()
            ->with("success", "Answer updated");
    }

    public function destroy(Answer $answer): RedirectResponse
    {
        $answer->delete();

        return redirect()
            ->back()
            ->with("success", "Answer deleted");
    }

    public function clone(Answer $answer, Question $question): RedirectResponse
    {
        $answer->cloneTo($question);

        return redirect()
            ->back()
            ->with("success", "Answer cloned");
    }
}

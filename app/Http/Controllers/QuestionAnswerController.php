<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuestionAnswerController extends Controller
{
    public function index(Question $question): Response
    {
        return Inertia::render("Answer/Index", [
            "answers" => AnswerResource::collection($question->answers),
        ]);
    }

    public function store(Question $question, AnswerRequest $request): RedirectResponse
    {
        Answer::query()
            ->create($request->validated())
            ->question()->associate($question)
            ->save();

        return redirect()
            ->back()
            ->with("success", "Answer added successfully");
    }

    public function show(Answer $answer): Response
    {
        return Inertia::render("Answer/show", ["answer" => new AnswerResource($answer)]);
    }

    public function markAsCorrect(Answer $answer): RedirectResponse
    {
        $answer->question->correctAnswer()->associate($answer)->save();

        return redirect()
            ->back()
            ->with("success", "Answer marked as the correct one");
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
}

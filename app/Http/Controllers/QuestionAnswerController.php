<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * @throws AuthorizationException
     */
    public function store(Question $question, AnswerRequest $request): RedirectResponse
    {
        if ($question->isLocked) {
            throw new AuthorizationException();
        }

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

    /**
     * @throws AuthorizationException
     */
    public function markAsCorrect(Answer $answer): RedirectResponse
    {
        $this->authorize("modify", $answer);

        $answer->question->correctAnswer()->associate($answer)->save();

        return redirect()
            ->back()
            ->with("success", "Answer marked as the correct one");
    }

    /**
     * @throws AuthorizationException
     */
    public function update(AnswerRequest $request, Answer $answer): RedirectResponse
    {
        $this->authorize("modify", $answer);

        $answer->update($request->validated());

        return redirect()
            ->back()
            ->with("success", "Answer updated");
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Answer $answer): RedirectResponse
    {
        $this->authorize("destroy", $answer);

        $answer->delete();

        return redirect()
            ->back()
            ->with("success", "Answer deleted");
    }
}

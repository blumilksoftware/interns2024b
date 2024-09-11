<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CloseQuizSubmissionAction;
use App\Http\Resources\QuizSubmissionResource;
use App\Models\QuizSubmission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuizSubmissionController extends Controller
{
    public function show(QuizSubmission $quizSubmission): Response
    {
        $quizSubmission->load(["answerRecords.question.answers"]);

        return Inertia::render("User/Quiz", ["submission" => QuizSubmissionResource::make($quizSubmission)]);
    }

    public function close(QuizSubmission $quizSubmission, CloseQuizSubmissionAction $action): RedirectResponse
    {
        $action->execute($quizSubmission);

        return redirect()->route("submissions.result", $quizSubmission->id)->with("status", "Test został oddany.");
    }

    public function result(QuizSubmission $quizSubmission): Response
    {
        $quizSubmission->load(["answerRecords.question.answers", "quiz"]);

        return Inertia::render("User/QuizResult", [
            "submission" => QuizSubmissionResource::make($quizSubmission),
            "hasRanking" => $quizSubmission->quiz->isRankingPublished,
        ]);
    }
}

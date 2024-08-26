<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\QuizSubmissionResource;
use App\Models\QuizSubmission;
use Inertia\Inertia;
use Inertia\Response;

class QuizSubmissionController extends Controller
{
    public function show(QuizSubmission $quizSubmission): Response
    {
        $quizSubmission->load(["answerRecords.question.answers", "quiz"]);

        return Inertia::render("Submission", ["submission" => QuizSubmissionResource::make($quizSubmission)]);
    }
}

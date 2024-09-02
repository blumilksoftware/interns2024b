<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function index(Quiz $quiz): Response
    {
        $submissions = QuizSubmission::where("quiz_id", $quiz->id)
            ->with(["user.school"])
            ->get();

        $rankings = $submissions->map(function ($submission) {
            return [
                "user_id" => $submission->user->id,
                "user_name" => $submission->user->name,
                "user_surname" => $submission->user->surname,
                "school" => $submission->user->school->name ?? "Brak szkoły",
                "points" => $submission->points,
            ];
        });

        return Inertia::render("Admin/Ranking", [
            "quiz" => $quiz,
            "rankings" => $rankings,
        ]);
    }
}

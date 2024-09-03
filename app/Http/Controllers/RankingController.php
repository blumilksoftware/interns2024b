<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\RankingResource;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function index(Quiz $quiz): Response
    {
        $submissions = QuizSubmission::where("quiz_id", $quiz->id)
            ->with("user.school")
            ->get();

        $rankings = $submissions->map(fn($submission) => new RankingResource($submission));

        return Inertia::render("Admin/Ranking", [
            "quiz" => $quiz,
            "rankings" => $rankings,
        ]);
    }
}

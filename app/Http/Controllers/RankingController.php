<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\PublishQuizRankingAction;
use App\Actions\UnpublishQuizRankingAction;
use App\Http\Resources\RankingResource;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function index(Quiz $quiz): Response
    {
        $this->authorize("viewAdminRanking", $quiz);

        $submissions = QuizSubmission::query()
            ->where("quiz_id", $quiz->id)
            ->with("user.school")
            ->get();

        $rankings = $submissions->map(fn($submission): RankingResource => new RankingResource($submission));

        return Inertia::render("Admin/Ranking", [
            "quiz" => $quiz,
            "rankings" => $rankings,
        ]);
    }

    public function indexUser(Quiz $quiz): Response
    {
        $this->authorize("viewUserRanking", $quiz);

        $submissions = QuizSubmission::query()
            ->where("quiz_id", $quiz->id)
            ->with("user.school")
            ->get();

        $rankings = $submissions->map(fn($submission): RankingResource => new RankingResource($submission));

        return Inertia::render("User/Ranking", [
            "quiz" => $quiz,
            "rankings" => $rankings,
        ]);
    }

    public function publish(Quiz $quiz, PublishQuizRankingAction $publishQuizRankingAction): RedirectResponse
    {
        if (!$quiz->exists) {
            abort(404);
        }

        $this->authorize("publish", $quiz);

        $publishQuizRankingAction->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Ranking został opublikowany.");
    }

    public function unpublish(Quiz $quiz, UnpublishQuizRankingAction $unpublishQuizRankingAction): RedirectResponse
    {
        if (!$quiz->exists) {
            abort(404);
        }

        $this->authorize("publish", $quiz);

        $unpublishQuizRankingAction->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Ranking został wycofany.");
    }
}

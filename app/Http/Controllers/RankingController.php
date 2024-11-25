<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\PublishQuizRankingAction;
use App\Actions\UnpublishQuizRankingAction;
use App\Http\Resources\QuizResource;
use App\Http\Resources\RankingResource;
use App\Models\Quiz;
use App\Models\UserQuiz;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function index(Quiz $quiz): Response
    {
        $userQuizzes = UserQuiz::query()
            ->where("quiz_id", $quiz->id)
            ->with("user.school")
            ->get();

        return Inertia::render("Admin/Ranking", [
            "quiz" => QuizResource::make($quiz),
            "rankings" => RankingResource::collection($userQuizzes),
        ]);
    }

    public function indexUser(Quiz $quiz): Response
    {
        $this->authorize("viewUserRanking", $quiz);

        $userQuizzes = UserQuiz::query()
            ->where("quiz_id", $quiz->id)
            ->with("user.school")
            ->get();

        return Inertia::render("User/Ranking", [
            "quiz" => QuizResource::make($quiz),
            "rankings" => RankingResource::collection($userQuizzes),
        ]);
    }

    public function publish(Quiz $quiz, PublishQuizRankingAction $publishQuizRankingAction): RedirectResponse
    {
        $publishQuizRankingAction->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Ranking został opublikowany.");
    }

    public function unpublish(Quiz $quiz, UnpublishQuizRankingAction $unpublishQuizRankingAction): RedirectResponse
    {
        $unpublishQuizRankingAction->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Ranking został wycofany.");
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DisqualifyUserAction;
use App\Actions\PublishQuizRankingAction;
use App\Actions\UndisqualifyUserAction;
use App\Actions\UnpublishQuizRankingAction;
use App\Http\Requests\DisqualifyUserRequest;
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

        $quizzes = Quiz::query()
            ->select("id", "title")
            ->whereNotNull("scheduled_at")
            ->where("scheduled_at", ">", now())
            ->whereNotNull("locked_at")
            ->get();

        return Inertia::rendeQuestior("Admin/Ranking", [
            "quiz" => QuizResource::make($quiz),
            "quizzes" => $quizzes,
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

    public function disqualify(DisqualifyUserAction $action, UserQuiz $userQuiz, DisqualifyUserRequest $request) {
        $this->authorize("disqualify", $userQuiz);

        $action->execute($userQuiz,  $request->validated()["reason"], $request->validated()["sendEmail"]);

        return redirect()
            ->back()
            ->with("status", "Użytkownik został zdyskwalifikowany.");
    }

    public function undisqualify(UndisqualifyUserAction $action, UserQuiz $userQuiz) {
        $this->authorize("undisqualify", $userQuiz);

        $action->execute($userQuiz);

        return redirect()
            ->back()
            ->with("status", "Dyskwalifikacją użytkownika została cofnięta.");
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CloseUserQuizAction;
use App\Http\Resources\UserQuizResource;
use App\Models\UserQuiz;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserQuizController extends Controller
{
    public function show(UserQuiz $userQuiz): Response
    {
        $userQuiz->load(["userQuestions.question.answers"]);

        return Inertia::render("User/Quiz", ["userQuiz" => UserQuizResource::make($userQuiz)]);
    }

    public function close(UserQuiz $userQuiz, CloseUserQuizAction $action): RedirectResponse
    {
        $action->execute($userQuiz);

        return redirect()->route("userQuizzes.result", $userQuiz->id)->with("status", "Test został oddany.");
    }

    public function result(UserQuiz $userQuiz): Response
    {
        $userQuiz->load(["userQuestions.question.answers", "quiz"]);

        return Inertia::render("User/QuizResult", [
            "userQuiz" => UserQuizResource::make($userQuiz),
            "hasRanking" => $userQuiz->quiz->isRankingPublished,
        ]);
    }
}

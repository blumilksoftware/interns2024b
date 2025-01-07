<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Http\Resources\RankingResource;
use App\Models\Quiz;
use App\Models\UserQuiz;
use Inertia\Inertia;
use Inertia\Response;

class GroupInviteController extends Controller
{
    public function index(Quiz $from, Quiz $to): Response
    {
        $this->authorize("invite", $to);
        $this->authorize("viewAdminRanking", $from);

        $userQuizzes = UserQuiz::query()
            ->where("quiz_id", $from->id)
            ->with("user.school")
            ->get();

        $quizzes = Quiz::query()
            ->select("id", "title")
            ->whereNotNull("scheduled_at")
            ->where("scheduled_at", ">", now())
            ->whereNotNull("locked_at")
            ->get();

        $ranking_quizzes = Quiz::query()
            ->select("id", "title")
            ->whereNotNull("scheduled_at")
            ->where("scheduled_at", "<=", now())
            ->whereNotNull("locked_at")
            ->get();

        return Inertia::render("Admin/GroupInvite", [
            "rankings" => RankingResource::collection($userQuizzes),
            "to" => QuizResource::make($to),
            "from" => QuizResource::make($from),
            "fromQuizzes" => $ranking_quizzes,
            "toQuizzes" => $quizzes,
            "assigned" => $to->assignedUsers->pluck("id"),
        ]);
    }
}

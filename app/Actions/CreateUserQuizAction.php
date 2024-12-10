<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CloseUserQuizJob;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuestion;
use App\Models\UserQuiz;

class CreateUserQuizAction
{
    public function execute(Quiz $quiz, User $user): UserQuiz
    {
        $userQuiz = new UserQuiz();
        $userQuiz->closed_at = $quiz->closeAt;
        $userQuiz->quiz()->associate($quiz);
        $userQuiz->user()->associate($user);
        $userQuiz->save();

        foreach ($quiz->questions as $question) {
            $userQuestion = new UserQuestion();
            $userQuestion->userQuiz()->associate($userQuiz);
            $userQuestion->question()->associate($question);
            $userQuestion->save();
        }

        if ($quiz->isClosingToday()) {
            CloseUserQuizJob::dispatch($quiz)->delay($quiz->closeAt);
        }

        return $userQuiz;
    }
}

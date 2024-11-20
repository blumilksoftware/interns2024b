<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Answer;
use App\Models\UserQuestion;
use App\Models\User;

class UserQuestionPolicy
{
    public function answer(User $user, UserQuestion $userQuestion, Answer $answer): bool
    {
        $userQuiz = $userQuestion->userQuiz;

        return !$userQuiz->isClosed && $userQuiz->user_id === $user->id && $userQuestion->question_id === $answer->question_id;
    }
}
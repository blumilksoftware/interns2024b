<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserQuiz;

class UserQuizPolicy
{
    public function view(User $user, UserQuiz $userQuiz): bool
    {
        return $user->id === $userQuiz->user_id;
    }

    public function close(User $user, UserQuiz $userQuiz): bool
    {
        return $user->id === $userQuiz->user_id && !$userQuiz->isClosed;
    }

    public function result(User $user, UserQuiz $userQuiz): bool
    {
        return $user->id === $userQuiz->user_id && $userQuiz->isClosed;
    }

    public function disqualify(User $user, UserQuiz $userQuiz): bool
    {
        return $userQuiz->disqualification()->doesntExist();
    }

    public function undisqualify(User $user, UserQuiz $userQuiz): bool
    {
        return $userQuiz->disqualification()->exists();
    }
}

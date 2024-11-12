<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\UserQuiz;
use App\Models\User;

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
}

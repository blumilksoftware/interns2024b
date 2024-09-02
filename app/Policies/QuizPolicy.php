<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    public function update(User $user, Quiz $quiz): bool
    {
        return !$quiz->isLocked;
    }

    public function delete(User $user, Quiz $quiz): bool
    {
        if ($user->hasRole("super_admin")) {
            return true;
        }

        return !$quiz->isLocked;
    }

    public function submit(User $user, Quiz $quiz): bool
    {
        return $quiz->isLocked;
    }

    public function lock(User $user, Quiz $quiz): bool
    {
        return $quiz->canBeLocked;
    }

    public function unlock(User $user, Quiz $quiz): bool
    {
        return $quiz->canBeUnlocked;
    }

    public function assign(User $user, Quiz $quiz): bool
    {
        return $quiz->isLocked && !$quiz->isPublished && !$quiz->hasSubmissionsFrom($user);
    }
}

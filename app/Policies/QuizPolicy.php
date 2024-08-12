<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    public function modify(User $user, Quiz $quiz): bool
    {
        return !$quiz->isLocked;
    }

    public function destroy(User $user, Quiz $quiz): bool
    {
        return !$quiz->isLocked;
    }
}

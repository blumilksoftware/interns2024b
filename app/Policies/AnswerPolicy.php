<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;

class AnswerPolicy
{
    public function modify(User $user, Answer $answer): bool
    {
        return !$answer->isLocked;
    }

    public function destroy(User $user, Answer $answer): bool
    {
        return !$answer->isLocked;
    }
}

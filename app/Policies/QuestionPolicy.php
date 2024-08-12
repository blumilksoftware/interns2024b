<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function modify(User $user, Question $question): bool
    {
        return !$question->isLocked;
    }

    public function destroy(User $user, Question $question): bool
    {
        return !$question->isLocked;
    }
}

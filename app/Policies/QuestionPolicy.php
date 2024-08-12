<?php

declare(strict_types=1);

namespace App\Policies;

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

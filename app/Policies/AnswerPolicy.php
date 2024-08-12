<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

class AnswerPolicy
{
    public function update(User $user, Answer $answer): bool
    {
        return !$answer->isLocked;
    }

    public function delete(User $user, Answer $answer): bool
    {
        return !$answer->isLocked;
    }

    public function create(User $user, Question $question): bool
    {
        return !$question->isLocked;
    }

    public function clone(User $user, Answer $answer, Question $question): bool
    {
        return !$question->isLocked;
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;

class QuestionPolicy
{
    public function update(User $user, Question $question): bool
    {
        return !$question->isLocked;
    }

    public function delete(User $user, Question $question): bool
    {
        return !$question->isLocked;
    }

    public function create(User $user, Quiz $quiz): bool
    {
        return !$quiz->isLocked;
    }

    public function clone(User $user, Question $question, Quiz $quiz): bool
    {
        return !$quiz->isLocked;
    }
}

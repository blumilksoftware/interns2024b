<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\QuizSubmission;
use App\Models\User;

class QuizSubmissionPolicy
{
    public function view(User $user, QuizSubmission $quizSubmission): bool
    {
        return $user->id === $quizSubmission->user_id;
    }
}

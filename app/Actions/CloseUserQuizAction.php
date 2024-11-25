<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserQuiz;
use Carbon\Carbon;

class CloseUserQuizAction
{
    public function execute(UserQuiz $userQuiz): void
    {
        $userQuiz->closed_at = Carbon::now();
        $userQuiz->save();
    }
}

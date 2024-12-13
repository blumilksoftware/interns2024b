<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;

class UnlockQuizAction
{
    public function execute(Quiz $quiz): void
    {
        $quiz->locked_at = null;
        $quiz->save();
    }
}

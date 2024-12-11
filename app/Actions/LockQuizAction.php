<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CloseUserQuizJob;
use App\Models\Quiz;
use Carbon\Carbon;

class LockQuizAction
{
    public function execute(Quiz $quiz): void
    {
        $quiz->locked_at = Carbon::now();
        $quiz->save();

        if ($quiz->isClosingToday()) {
            CloseUserQuizJob::dispatch($quiz)->delay($quiz->closeAt);
        }
    }
}

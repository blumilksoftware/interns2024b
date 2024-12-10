<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\UserQuizClosed;
use App\Models\Quiz;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseUserQuizJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public function __construct(
        protected Quiz $quiz,
    ) {}

    public function handle(): void
    {
        foreach ($this->quiz->userQuizzes as $userQuiz) {
            if (!$userQuiz->wasClosedManually()) {
                event(new UserQuizClosed($userQuiz));
            }
        }
    }
}

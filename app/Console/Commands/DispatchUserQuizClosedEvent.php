<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CloseUserQuizJob;
use App\Models\Quiz;
use Illuminate\Console\Command;

class DispatchUserQuizClosedEvent extends Command
{
    protected $signature = "app:dispatch-user-quiz-closed-event";
    protected $description = "Dispatches the UserQuizClosed event for quizzes that are closing today.";

    public function handle(): void
    {
        foreach (Quiz::all() as $quiz) {
            if ($quiz->isClosingToday()) {
                CloseUserQuizJob::dispatch($quiz)->delay($quiz->closeAt);
            }
        }
    }
}

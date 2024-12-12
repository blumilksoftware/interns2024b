<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CloseUserQuizJob;
use App\Models\Quiz;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class DispatchUserQuizClosedEvent extends Command
{
    protected $signature = "app:dispatch-user-quiz-closed-event";
    protected $description = "Dispatches the UserQuizClosed event for quizzes that are closing today.";

    public function handle(): void
    {
        foreach ($this->getClosingTodayQuizzes() as $quiz) {
            CloseUserQuizJob::dispatch($quiz)->delay($quiz->closeAt);
        }
    }

    /**
     * @return Collection<Quiz>
     */
    public function getClosingTodayQuizzes(): Collection
    {
        return Quiz::query()
            ->whereNotNull("locked_at")
            ->whereRaw("(scheduled_at + INTERVAL '1 minute' * duration) >= ?", [now()])
            ->whereRaw("DATE(scheduled_at + INTERVAL '1 minute' * duration) = ?", [today()])
            ->get();
    }
}

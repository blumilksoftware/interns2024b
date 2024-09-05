<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use Carbon\Carbon;

class PublishQuizRankingAction
{
    public function execute(Quiz $quiz): void
    {
        $quiz->ranking_published_at = Carbon::now();
        $quiz->save();
    }
}

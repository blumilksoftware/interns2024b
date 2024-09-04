<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;

class UnpublishQuizRanking
{
    public function execute(Quiz $quiz): void
    {
        $quiz->ranking_published_at = null;
        $quiz->save();
    }
}

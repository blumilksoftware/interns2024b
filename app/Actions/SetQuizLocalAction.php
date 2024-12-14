<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;

class SetQuizLocalAction
{
    public function execute(Quiz $quiz): void
    {
        $quiz->is_local = true;
        $quiz->save();
    }
}

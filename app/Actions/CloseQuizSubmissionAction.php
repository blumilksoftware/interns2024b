<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\QuizSubmission;
use Carbon\Carbon;

class CloseQuizSubmissionAction
{
    public function execute(QuizSubmission $submission): void
    {
        $submission->closed_at = Carbon::now();
        $submission->save();
    }
}

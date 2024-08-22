<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Answer;
use App\Models\AnswerRecord;
use App\Models\User;

class AnswerRecordPolicy
{
    public function answer(User $user, AnswerRecord $answerRecord, Answer $answer): bool
    {
        $submission = $answerRecord->quizSubmission;

        return !$submission->isClosed && $submission->user_id === $user->id && $answerRecord->question_id === $answer->question_id;
    }
}

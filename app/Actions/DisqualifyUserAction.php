<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Disqualification;
use App\Models\UserQuiz;
use App\Notifications\DisqualificationNotification;

class DisqualifyUserAction
{
    public function __construct() {}

    public function execute(UserQuiz $userQuiz, String $reason, bool $sendEmail = false): Disqualification
    {
        $disqualification = new Disqualification();
        $disqualification->reason = $reason;
        $disqualification->userQuiz()->associate($userQuiz);
        $disqualification->save();

        if ($sendEmail) {
            $userQuiz->user->notify(new DisqualificationNotification($userQuiz, $reason));
        }

        return $disqualification;
    }
}

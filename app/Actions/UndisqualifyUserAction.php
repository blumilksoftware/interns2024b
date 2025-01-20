<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Disqualification;
use App\Models\UserQuiz;
use App\Notifications\DisqualificationNotification;

class UndisqualifyUserAction
{
    public function __construct() {}

    public function execute(UserQuiz $userQuiz)
    {
        $userQuiz->disqualification()->delete();
    }
}

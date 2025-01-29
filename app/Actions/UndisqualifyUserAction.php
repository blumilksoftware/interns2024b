<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserQuiz;

class UndisqualifyUserAction
{
    public function __construct() {}

    public function execute(UserQuiz $userQuiz): void
    {
        $userQuiz->disqualification()->delete();
    }
}

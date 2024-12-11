<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Question;
use App\Models\UserQuestion;
use App\Models\UserQuiz;

class CreateUserQuestionAction
{
    public function execute(Question $question, UserQuiz $userQuiz): UserQuestion
    {
        $userQuestion = new UserQuestion();
        $userQuestion->userQuiz()->associate($userQuiz);
        $userQuestion->question()->associate($question);
        $userQuestion->save();

        return $userQuestion;
    }
}

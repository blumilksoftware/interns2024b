<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuiz;

class CreateUserQuizAction
{
    public function __construct(
        protected CreateUserQuestionAction $action,
    ) {}

    public function execute(Quiz $quiz, User $user): UserQuiz
    {
        $userQuiz = new UserQuiz();
        $userQuiz->closed_at = $quiz->closeAt;
        $userQuiz->quiz()->associate($quiz);
        $userQuiz->user()->associate($user);
        $userQuiz->save();

        foreach ($quiz->questions as $question) {
            $this->action->execute($question, $userQuiz);
        }

        return $userQuiz;
    }
}

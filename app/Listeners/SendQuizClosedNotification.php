<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserQuizClosed;
use App\Mail\QuizClosedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendQuizClosedNotification implements ShouldQueue
{
    public function handle(UserQuizClosed $event): void
    {
        $quiz = $event->userQuiz;

        Mail::to($quiz->user->email)->send(new QuizClosedMail($quiz));
    }
}

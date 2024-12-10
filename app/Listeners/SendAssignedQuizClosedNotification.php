<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AssignedQuizClosed;
use App\Mail\QuizClosedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendAssignedQuizClosedNotification implements ShouldQueue
{
    public function handle(AssignedQuizClosed $event): void
    {
        $quiz = $event->quiz;
        $user = $event->user;

        Mail::to($user->email)->send(new QuizClosedMail($user, $quiz));
    }
}

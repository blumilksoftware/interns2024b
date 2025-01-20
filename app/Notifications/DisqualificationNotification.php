<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\UserQuiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisqualificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public UserQuiz $userQuiz,
        public String $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Dyskwalifikacja z konkursu " . $this->userQuiz->quiz->title)
            ->view("emails.disqualification", [
                "user" => $notifiable,
                "userQuiz" => $this->userQuiz,
                "reason" => $this->reason,
            ]);
    }
}

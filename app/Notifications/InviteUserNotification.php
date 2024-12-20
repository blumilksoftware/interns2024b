<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Quiz $quiz,
    ) {}

    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Zaproszenie do Quizu")
            ->view("emails.auth.invite-user", [
                "user" => $notifiable,
                "quiz" => $this->quiz,
                "url" => url("/dashboard")
            ]);
    }
}

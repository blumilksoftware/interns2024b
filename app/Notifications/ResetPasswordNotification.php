<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $token,
    ) {}

    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url("/auth/password/reset/" . $this->token . "&email=" . urlencode($notifiable->email));

        return (new MailMessage())
            ->subject("Resetowanie hasła")
            ->view("emails.auth.reset-password", [
                "user" => $notifiable,
                "url" => $resetUrl,
            ]);
    }
}

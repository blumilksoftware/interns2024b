<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token,
    ) {}

    /**
     * @return array<int, string>
     */
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

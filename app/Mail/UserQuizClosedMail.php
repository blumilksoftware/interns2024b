<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\UserQuiz;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserQuizClosedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private UserQuiz $userQuiz,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->userQuiz->quiz->title . " — podsumowanie",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: "emails.quiz-closed",
            with: [
                "user" => $this->userQuiz->user,
                "userQuiz" => $this->userQuiz,
            ],
        );
    }
}

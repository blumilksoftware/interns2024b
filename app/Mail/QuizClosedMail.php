<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuizClosedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private User $user,
        private Quiz $quiz,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->quiz->title . " — podsumowanie",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: "emails.quiz-closed",
            with: [
                "user" => $this->user,
                "quiz" => $this->quiz,
            ],
        );
    }
}

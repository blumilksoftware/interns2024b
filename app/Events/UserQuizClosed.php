<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\UserQuiz;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserQuizClosed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public UserQuiz $userQuiz,
    ) {}
}

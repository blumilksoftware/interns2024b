<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInvitedToQuiz
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Quiz $quiz,
        public User $invitee,
        public User $inviter,
    ) {}
}

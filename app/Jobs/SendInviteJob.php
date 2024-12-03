<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Quiz;
use App\Models\User;
use App\Notifications\InviteUserNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInviteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public function __construct(
        protected User $user,
        protected Quiz $quiz,
    ) {}

    public function handle(): void
    {
        $this->user->notify(new InviteUserNotification($this->quiz));
    }
}

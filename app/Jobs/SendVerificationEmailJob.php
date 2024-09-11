<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Notifications\SendVerificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected User $user,
    ) {}

    public function handle(): void
    {
        $this->user->notify(new SendVerificationEmail());
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckPasswordWasForceChanged implements ShouldQueue
{
    public function handle(PasswordReset $event): void
    {
        if ($event->user->force_password_change) {
            $event->user->force_password_change = false;
            $event->user->save();
        }
    }
}

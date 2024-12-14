<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

class ForceChangePasswordAction
{
    public function execute(User $user): void
    {
        $user->force_password_change = true;
        $user->save();
    }
}

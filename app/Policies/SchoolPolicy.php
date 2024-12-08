<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
{
    public function update(User $user, School $school): bool
    {
        return !$school->is_disabled;
    }

    public function delete(User $user, School $school): bool
    {
        return $school->users()->count() === 0;
    }

    public function disable(User $user, School $school): bool
    {
        return !$school->is_disabled;
    }
}

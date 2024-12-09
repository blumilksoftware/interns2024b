<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
{
    public function update(User $user, School $school): bool
    {
        return !$school->is_admin_school && !$school->is_disabled;
    }

    public function delete(User $user, School $school): bool
    {
        return $school->users()->count() === 0 && !$school->is_disabled && !$school->is_admin_school;
    }

    public function disable(User $user, School $school): bool
    {
        return !$school->is_disabled && !$school->is_admin_school;
    }

    public function enable(User $user, School $school): bool
    {
        return $school->is_disabled && !$school->is_admin_school;
    }
}

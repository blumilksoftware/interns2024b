<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class CreateAdminAction
{
    public function execute(array $adminData): User
    {
        $school = School::query()->where(["is_admin_school" => true])->firstOrFail();

        $user = new User($adminData);
        $user->password = Hash::make($adminData["password"]);
        $user->school()->associate($school);
        $user->save();
        $user->syncRoles("admin");
        event(new Registered($user));

        return $user;
    }
}

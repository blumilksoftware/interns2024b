<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = config("roles.roles");

        foreach ($roles as $role) {
            Role::firstOrCreate(["name" => $role]);
        }
    }
}

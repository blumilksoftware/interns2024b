<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(["name" => "super-admin", "guard_name" => "web"]);
        Role::create(["name" => "admin", "guard_name" => "web"]);
        Role::create(["name" => "user", "guard_name" => "web"]);
    }
}

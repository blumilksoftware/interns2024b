<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(["name" => "super-admin", "guard_name" => "web"]);
        Role::firstOrCreate(["name" => "admin", "guard_name" => "web"]);
        Role::firstOrCreate(["name" => "user", "guard_name" => "web"]);
    }
}

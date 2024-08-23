<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRoleIfNotExists("super-admin");
        $this->createRoleIfNotExists("admin");
        $this->createRoleIfNotExists("user");
    }

    private function createRoleIfNotExists(string $roleName): void
    {
        if (!Role::where("name", $roleName)->exists()) {
            Role::create(["name" => $roleName, "guard_name" => "web"]);
        }
    }
}

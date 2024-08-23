<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole("super-admin");
        $admin = User::factory()->create();
        $admin->assignRole("admin");
        $user = User::factory()->create();
        $user->assignRole("user");
    }
}

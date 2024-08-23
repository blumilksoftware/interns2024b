<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create()->assignRole("super-admin");
        User::factory()->create()->assignRole("admin");
        User::factory()->create()->assignRole("user");
    }
}
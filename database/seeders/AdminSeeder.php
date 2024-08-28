<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->superAdmin()->create(["email" => "superadmin@example.com"]);
        User::factory()->admin()->create(["email" => "admin@example.com"]);
    }
}

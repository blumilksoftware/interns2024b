<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->superAdmin()->create();
        User::factory()->admin()->create();
        User::factory()->create();
    }
}

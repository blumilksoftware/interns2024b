<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            UserQuizSeeder::class,
        ]);
        Quiz::factory()->create([
            "title" => "quiz_name",
            "scheduled_at" => Carbon::now()->addMinutes(60),
            "duration" => 120,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\AnswerRecord;
use App\Models\Question;
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
        ]);
        Quiz::factory()->create([
            "name" => "quiz_name",
            "scheduled_at" => Carbon::now()->addMinutes(60),
            "duration" => 120,
        ]);
    }
}

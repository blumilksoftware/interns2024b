<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create();
        Quiz::factory()->create();
        Answer::factory()->create();
    }
}

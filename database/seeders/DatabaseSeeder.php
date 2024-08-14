<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AnswerRecord;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AnswerRecord::factory()->create();
    }
}

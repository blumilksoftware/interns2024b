<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
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
        ]);

        Quiz::factory()
            ->has(Question::factory()->count(5)->has(Answer::factory()->count(4)))
            ->count(3)
            ->create(["locked_at" => null, "duration" => 1, "scheduled_at" => Carbon::now()->addHour()]);

        Quiz::factory()
            ->has(Question::factory()->count(5)->has(Answer::factory()->count(4)))
            ->count(3)
            ->create(["locked_at" => null, "duration" => 1, "scheduled_at" => Carbon::now()->addMinutes(2)]);

        foreach (Quiz::all() as $quiz) {
            PublishedQuizSeeder::selectRandomCorrectAnswer($quiz);
        }
    }
}

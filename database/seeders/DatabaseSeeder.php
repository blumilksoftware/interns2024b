<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\School;
use App\Models\User;
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
            ->has(Question::factory()->has(Answer::factory()->count(4)))
            ->create(["locked_at" => Carbon::now()->subHour(), "duration" => 60, "scheduled_at" => Carbon::now()]);

        Quiz::factory()
            ->has(Question::factory()->count(5)->has(Answer::factory()->count(4)))
            ->count(3)
            ->create(["locked_at" => null, "duration" => 1, "scheduled_at" => Carbon::now()->addHour()]);

        Quiz::factory()
            ->has(Question::factory()->count(5)->has(Answer::factory()->count(4)))
            ->count(3)
            ->create(["locked_at" => null, "duration" => 1, "scheduled_at" => Carbon::now()->addMinutes(2)]);

        $archivedQuiz = Quiz::factory()
            ->has(Question::factory()->has(Answer::factory()->count(4)))
            ->create(["locked_at" => Carbon::now()->subDays(2), "duration" => 60, "scheduled_at" => Carbon::now()->subDay()]);

        foreach (Quiz::all() as $quiz) {
            PublishedQuizSeeder::selectRandomCorrectAnswer($quiz);
        }

        User::factory()->count(10)->has(School::factory())->create();
        User::factory()->count(10)->has(School::factory())->create();
        User::factory()->count(10)->has(School::factory())->create();

        foreach (User::query()->role("user")->get() as $user) {
            UserQuizSeeder::createUserQuizForUser($archivedQuiz, $user, null);
        }
    }
}

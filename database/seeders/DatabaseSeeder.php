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
            UserQuizSeeder::class,
        ]);
        // $quiz = Quiz::factory()->create();
        // $questions = Question::factory()->count(10)->create(["quiz_id" => $quiz->id]);
        // foreach ($questions as $question) {
        //     Answer::factory()->count(4)->create(["question_id" => $question->id]);
        // }
        // AnswerRecord::factory()->create();

        Quiz::factory()->create([
            "name" => "unlocked"
        ]);

        Quiz::factory()->create([
            "name" => "locked",
            "locked_at" => Carbon::now()->subMinutes(10),
            "scheduled_at" => Carbon::now()->addMinutes(60),
            "duration" => 120,
        ]);

        Quiz::factory()->create([
            "name" => "published",
            "locked_at" => Carbon::now()->subMinutes(60),
            "scheduled_at" => Carbon::now()->subMinutes(10),
            "duration" => 120,
        ]);
    }
}

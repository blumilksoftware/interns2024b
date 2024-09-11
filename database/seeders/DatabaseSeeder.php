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
            UserSeeder::class,
            UserQuizSeeder::class,
        ]);

        Quiz::factory()->locked()->count(5)->create(["scheduled_at" => Carbon::now()->addMonth()]);
        Quiz::factory()->locked()->create(["name" => "6 Minutes", "scheduled_at" => Carbon::now(), "duration" => 6]);

        $quiz = Quiz::factory()->locked()->create(["title" => "Test Quiz", "scheduled_at" => Carbon::now(), "duration" => 2]);
        $questions = Question::factory()->count(10)->create(["quiz_id" => $quiz->id]);

        foreach ($questions as $question) {
            $answers = Answer::factory()->count(4)->create(["question_id" => $question->id]);
            $question->correct_answer_id = $answers[0]->id;
            $question->save();
        }
    }
}

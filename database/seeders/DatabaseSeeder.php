<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
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

        Quiz::factory()->locked()->count(5)->create(["scheduled_at" => Carbon::now()->addMonth()]);

        $quiz = Quiz::factory()->locked()->create(["name" => "Test Quiz", "scheduled_at" => Carbon::now(), "duration" => 2]);
        $questions = Question::factory()->count(10)->create(["quiz_id" => $quiz->id]);

        foreach ($questions as $question) {
            $answers = Answer::factory()->count(4)->create(["question_id" => $question->id]);
            $question->correct_answer_id = $answers[0]->id;
            $question->save();
        }

        foreach (User::query()->role("user")->get() as $user) {
            $submission = $quiz->createSubmission($user);

            foreach ($submission->answerRecords as $answer) {
                $answer->answer()->associate($answer->question->answers->random());
                $answer->save();
            }
        }

        User::factory()->count(10)->create();
    }
}

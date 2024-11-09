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

        Quiz::factory()->count(2)->create(["scheduled_at" => Carbon::now()->addMonth()]);
        Quiz::factory()->locked()->count(2)->create(["scheduled_at" => Carbon::now()->subMonth(), "duration" => 6]);

        $quizzes = Quiz::factory()->count(4)->locked()->create(["scheduled_at" => Carbon::now()->addDay()]);

        foreach ($quizzes as $quiz) {
            $questions = Question::factory()->count(4)->create(["quiz_id" => $quiz->id]);

            foreach ($questions as $question) {
                $answers = Answer::factory()->count(4)->create(["question_id" => $question->id]);
                $question->correct_answer_id = $answers[rand(0, 3)]->id;
                $question->save();
            }
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

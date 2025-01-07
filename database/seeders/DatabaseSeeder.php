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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
        ]);

        $archivedQuiz = Quiz::factory(["title" => "Konkurs wiedzy o WH40K"])->create(["locked_at" => Carbon::now()->subDays(2), "duration" => 60, "scheduled_at" => Carbon::now()->subDay()]);

        $this->createQuestion($archivedQuiz,
            "W którym tysiąc leciu dzieje się aktualna akcja świata Warhammera 40,000?",
            ["39", "40", "41"],
            "42"
        );

        $this->createQuestion($archivedQuiz,
            "W jaki sposób podróżuje większość statków kosmicznych?",
            ["prędkość światła", "przez czarne dziury", "hipernapęd"],
            "przez Osnowę (Warp)"
        );

        $this->createQuestion($archivedQuiz,
            "Który z bogów chaosu jest najmłodszy?",
            ["Nurgle", "Gork", "Khorn"],
            "Slaanesh"
        );

        $this->createQuestion($archivedQuiz,
            "Ile legionów kosmicznych marines stworzył imperator podczas 1 fundacji?",
            ["5", "100", "Imperator nie zna limitów"],
            "20"
        );

        $this->createQuestion($archivedQuiz,
            "Jak nazywają się bogowie orków?",
            ["Tork i Hork", "Ork i Mork", "Kork i Tork"],
            "Gork i Mork"
        );

        $this->createQuestion($archivedQuiz,
            "Jak nazywa się planeta na której powstał Kult Mechanikus?",
            ["Terra", "Mechan Prime", "Caliban"],
            "Mars"
        );

        $this->createQuestion($archivedQuiz,
            "Jak nazywa się jedna z najstarszych ras przypominająca Elfy?",
            ["Tau", "Nekroni", "Orkowie", "Ludzie", "Mózgole"],
            "Eldarzy"
        );

        $this->createQuestion($archivedQuiz,
            "Która rasa stworzyła doktrynę większego dobra?",
            ["Eldarzy", "Nekroni", "Orkowie", "Ludzie", "Mózgole"],
            "Tau"
        );

        $this->createQuestion($archivedQuiz,
            "Która rasa nie jest organiczna",
            ["Eldarzy", "Tau", "Orkowie", "Ludzie", "Mózgole"],
            "Nekroni"
        );

        $this->createQuestion($archivedQuiz,
            "Jak nazywa się patriarcha który rozpoczął wojne domową w imperium przeciw imperatorowi?",
            ["Magnus", "Lion el Johnson", "Leman Russ", "Ghazghkull Mag Uruk Thraka"],
            "Horus"
        );

        $this->createQuestion($archivedQuiz,
            "Jakiej jednoski jest to motto: \"W życiu, wojna. W śmierci, pokój. W życiu, wstyd. W śmierci, pokuta.\"",
            ["Adeptus Astartes", "Adeptus Astartes Primaris", "Adepta Sororitas", "Ordo Hereticus", "Ordo Xenos"],
            "Kriegańskie Korpusy Śmierci"
        );

        $this->createQuestion($archivedQuiz,
            "Po jakim wydarzeniu bogowie Gork i Mork objawili się Ghazghkull'owi mag Uruk Thraka?",
            ["Śmierć", "Wygrana wojna", "Przejęcie dowodzenia nad orkami", "Zabójstwo imperatora ludzi"],
            "Utrata połowy mózgu"
        );


        foreach (Quiz::all() as $quiz) {
            PublishedQuizSeeder::selectRandomCorrectAnswer($quiz);
        }

        for ($i = 0; $i < 5; $i++) {
            $school = School::factory([
                "name" => "Szkoła numer " . $i + 1 . " w Legnicy",
            ])->create();
            User::factory(["school_id" => $school->id])->count(10)->create();
        }

        foreach (User::query()->role("user")->get() as $user) {
            UserQuizSeeder::createUserQuizForUser($archivedQuiz, $user, null);
        }

        $user1 = User::firstOrCreate(
            ["email" => "user1@example.com"],
            [
                "firstname" => "Example",
                "surname" => "User One",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("interns2024b"),
                "remember_token" => Str::random(10),
                "school_id" => School::factory()->create()->id,
            ],
        );

        $user1->syncRoles("user");

        $user2 = User::firstOrCreate(
            ["email" => "user2@example.com"],
            [
                "firstname" => "Example",
                    "surname" => "User Two",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("interns2024b"),
                "remember_token" => Str::random(10),
                "school_id" => School::factory()->create()->id,
            ],
        );

        $user2->syncRoles("user");

        $user3 = User::firstOrCreate(
            ["email" => "user3@example.com"],
            [
                "firstname" => "Example",
                "surname" => "User Three",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("interns2024b"),
                "remember_token" => Str::random(10),
                "school_id" => School::factory()->create()->id,
            ],
        );

        $user3->syncRoles("user");
    }

    function createQuestion(Quiz $quiz, string $text, array $invalid_answers, string $correct)
    {
        $question = Question::factory([
            "text" => $text,
            "quiz_id" => $quiz->id,
        ])->create();

        foreach ($invalid_answers as $answer) {
            Answer::factory([
                "text" => $answer,
                "question_id" => $question->id,
            ])->create();
        }

        $answer = Answer::factory([
            "text" => $correct,
            "question_id" => $question->id,
        ])->create();

        $question->correctAnswer()->associate($answer);
        $question->save();
    }
}

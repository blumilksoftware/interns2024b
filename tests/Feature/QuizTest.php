<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10));

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testAdminCanViewQuizzes(): void
    {
        $quizzes = Quiz::factory()->count(2)->create();
        Question::factory()->count(5)->create(["quiz_id" => $quizzes[0]->id]);
        Question::factory()->count(5)->create(["quiz_id" => $quizzes[1]->id]);

        $this->assertDatabaseCount("quizzes", 2);
        $this->assertDatabaseCount("questions", 10);

        $this->actingAs($this->admin)
            ->get("/admin/quizzes")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/Quizzes")
                    ->has("quizzes", 2)
                    ->has("quizzes.0.questions", 5)
                    ->has("quizzes.1.questions", 5),
            );
    }

    public function testUserCanCreateQuiz(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz", "scheduled_at" => "2024-02-10 11:40:00"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "name" => "Example quiz",
            "scheduled_at" => "2024-02-10 11:40:00",
        ]);
    }

    public function testAdminCanCreateQuizWithoutDate(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10));

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "name" => "Example quiz",
            "scheduled_at" => null,
        ]);
    }

    public function testAdminCanCreateMultipleQuizzes(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "Example quiz 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 1"]);
        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 2"]);
        $this->assertDatabaseHas("quizzes", ["name" => "Example quiz 2"]);
    }

    public function testAdminCannotCreateInvalidQuiz(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "scheduled_at" => "invalid format"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "scheduled_at" => "2022-01-01 01:01:01"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "duration" => -100])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->from("/")
            ->post("/admin/quizzes", ["name" => "correct", "duration" => 0])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->assertDatabaseCount("quizzes", 0);
    }

    public function testAdminCanEditQuiz(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        $answer = Answer::factory()->create(["question_id" => $question->id]);

        $data = [
            "name" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "questions" => [
                [
                    "id" => $question->id,
                    "text" => "Question's content 1",
                    "answers" => [
                        [
                            "id" => $answer->id,
                            "text" => "Answer's content 1",
                            "correct" => true,
                        ],
                        [
                            "text" => "Answer's content 2",
                        ],
                    ],
                ],
                [
                    "text" => "Question's content 2",
                    "answers" => [
                        [
                            "text" => "Answer's content 3",
                        ],
                        [
                            "text" => "Answer's content 4",
                            "correct" => true,
                        ],
                    ],
                ],
            ],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "name" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
        ]);

        $this->assertDatabaseHas("questions", [
            "id" => $question->id,
            "quiz_id" => $quiz->id,
            "text" => "Question's content 1",
            "correct_answer_id" => $answer->id,
        ]);

        $this->assertDatabaseHas("questions", [
            "quiz_id" => $quiz->id,
            "text" => "Question's content 2",
        ]);

        $this->assertDatabaseHas("answers", [
            "question_id" => $question->id,
            "text" => "Answer's content 1",
        ]);

        $this->assertDatabaseHas("answers", [
            "question_id" => $question->id,
            "text" => "Answer's content 2",
        ]);

        $this->assertDatabaseHas("answers", [
            "question_id" => $question->id + 1,
            "text" => "Answer's content 3",
        ]);

        $this->assertDatabaseHas("answers", [
            "question_id" => $question->id + 1,
            "text" => "Answer's content 4",
        ]);
    }

    public function testAdminCannotEditQuizWithNonExistentAnswer(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);

        $data = [
            "name" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "questions" => [
                [
                    "id" => $question->id,
                    "text" => "Question's content 1",
                    "answers" => [
                        [
                            "id" => 0,
                            "text" => "Answer's content 1",
                            "correct" => true,
                        ],
                        [
                            "text" => "Answer's content 2",
                        ],
                    ],
                ],
            ],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertStatus(404);
    }

    public function testAdminCannotEditQuizWithQuestionThatHasMoreThanOneCorrectAnswer(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);

        $data = [
            "name" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "questions" => [
                [
                    "id" => 0,
                    "text" => "Question's content 1",
                    "answers" => [
                        [
                            "text" => "Answer's content 1",
                            "correct" => true,
                        ],
                        [
                            "text" => "Answer's content 2",
                            "correct" => true,
                        ],
                    ],
                ],
            ],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertRedirect("/")->assertSessionHasErrors(
                [
                    "questions" => "Każde pytanie może mieć maksymalnie jedną poprawną odpowiedź."],
            );
    }

    public function testAdminCannotEditQuizWithNonExistentQuestion(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);

        $data = [
            "name" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "questions" => [
                [
                    "id" => 0,
                    "text" => "Question's content 1",
                ],
            ],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertStatus(404);
    }

    public function testAdminCannotEditQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/1", ["name" => "New quiz"])
            ->assertStatus(404);
    }

    public function testAdminCannotMakeInvalidEdit(): void
    {
        $quiz = Quiz::factory()->create(["name" => "Old quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["name"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "scheduled_at" => "invalid format"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "scheduled_at" => "2022-01-01 01:01:01"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "duration" => -100])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "correct", "duration" => 0])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->assertDatabaseHas("quizzes", ["name" => "Old quiz"]);
    }

    public function testAdminCannotEditLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["name" => "Old quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["name" => "New quiz"])
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["name" => "Old quiz"]);
    }

    public function testAdminCanDeleteQuiz(): void
    {
        $quiz = Quiz::factory()->create(["name" => "quiz"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        Answer::factory()->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("quizzes", ["name" => "quiz"]);
        $this->assertDatabaseCount("quizzes", 0);
        $this->assertDatabaseCount("questions", 0);
        $this->assertDatabaseCount("answers", 0);
    }

    public function testAdminCannotDeleteLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["name" => "quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["name" => "quiz"]);
    }

    public function testAdminCannotDeleteQuestionThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/1")
            ->assertStatus(404);
    }

    public function testAdminCanCopyQuiz(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);

        Answer::factory()->count(10)->create(["question_id" => $questions[0]->id]);
        Answer::factory()->count(10)->create(["question_id" => $questions[1]->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 2);
        $this->assertDatabaseCount("answers", 20);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/clone")
            ->assertRedirect("/");

        $this->assertDatabaseCount("quizzes", 2);
        $this->assertDatabaseCount("questions", 4);
        $this->assertDatabaseCount("answers", 40);
    }

    public function testAdminCanCopyLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->assertDatabaseCount("quizzes", 1);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/clone")
            ->assertRedirect("/");

        $this->assertDatabaseCount("quizzes", 2);
    }

    public function testAdminCannotCopyQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/2/clone")
            ->assertStatus(404);
    }

    public function testAdminCanLockQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["locked_at" => null]);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/lock")
            ->assertRedirect("/")
            ->assertSessionHas(["status" => "Test oznaczony jako gotowy do publikacji"]);
    }

    public function testAdminCannotLockQuizThatIsLocked(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/lock")
            ->assertStatus(403);
    }

    public function testAdminCannotLockQuizWithPastScheduledTime(): void
    {
        $quiz = Quiz::factory()->locked()->create([
            "locked_at" => null,
            "scheduled_at" => Carbon::now()->subMinutes(60),
        ]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/lock")
            ->assertStatus(403);
    }

    public function testAdminCannotLockQuizThatCannotBeScheduled(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/lock")
            ->assertStatus(403);
    }

    public function testAdminCannotLockQuizThatHasQuestionsWithoutCorrectAnswer(): void
    {
        $answer = Answer::factory()->locked()->create();
        $answer->question->correct_answer_id = null;
        $answer->question->save();

        $quiz = $answer->question->quiz;
        $quiz->locked_at = null;
        $quiz->save();

        $this->actingAs($this->user)
            ->from("/")
            ->post("admin/quizzes/{$quiz->id}/lock")
            ->assertStatus(403);
    }

    public function testAdminCanUnlockQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/unlock")
            ->assertRedirect("/")
            ->assertSessionHas(["status" => "Publikacja testu została wycofana"]);
    }

    public function testAdminCannotUnlockQuizThatIsNotLocked(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/unlock")
            ->assertStatus(403);
    }

    public function testAdminCannotUnlockQuizWithPastScheduledTime(): void
    {
        $quiz = Quiz::factory()->locked()->create([
            "scheduled_at" => Carbon::now()->subMinutes(60),
        ]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/unlock")
            ->assertStatus(403);
    }

    public function testUserCanStartQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $response = $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start");

        $submission = QuizSubmission::query()->where([
            "user_id" => $this->user->id,
            "quiz_id" => $quiz->id,
        ])->firstOrFail();

        $response->assertRedirect("/submissions/$submission->id/");
    }

    public function testUserCannotStartAlreadyStartedQuiz(): void
    {
        $submission = QuizSubmission::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$submission->quiz->id}/start")
            ->assertRedirect("/submissions/{$submission->id}/");

        $this->assertDatabaseCount("quiz_submissions", 1);
    }

    public function testUserCannotStartUnlockedQuiz(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start")
            ->assertStatus(403);

        $this->assertDatabaseCount("quiz_submissions", 0);
    }

    public function testUserCannotAccessToCrud(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->get("/admin/quizzes")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/clone")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/lock")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/unlock")
            ->assertStatus(403);
    }
}

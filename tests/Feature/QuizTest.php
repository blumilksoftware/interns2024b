<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuiz;
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
                    ->has("quizzes.data", 2)
                    ->has("quizzes.data.0.questions", 5)
                    ->has("quizzes.data.1.questions", 5),
            );
    }

    public function testAdminCanCreateQuiz(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["title" => "Example quiz", "description" => "test", "scheduled_at" => "2024-02-10 11:40:00"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "title" => "Example quiz",
            "scheduled_at" => "2024-02-10 11:40:00",
            "description" => "test",
        ]);
    }

    public function testAdminCanCreateQuizWithoutDate(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10));

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["title" => "Example quiz"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "title" => "Example quiz",
            "scheduled_at" => null,
        ]);
    }

    public function testAdminCanCreateMultipleQuizzes(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", ["title" => "Example quiz 1"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes", ["title" => "Example quiz 2"])
            ->assertRedirect("/");

        $this->from("/")
            ->post("/admin/quizzes", ["title" => "Example quiz 3"])
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", ["title" => "Example quiz 1"]);
        $this->assertDatabaseHas("quizzes", ["title" => "Example quiz 2"]);
        $this->assertDatabaseHas("quizzes", ["title" => "Example quiz 2"]);
    }

    public function testAdminCannotCreateInvalidQuiz(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes", [])
            ->assertRedirect("/")->assertSessionHasErrors(["title"]);

        $this->from("/")
            ->post("/admin/quizzes", ["title" => false])
            ->assertRedirect("/")->assertSessionHasErrors(["title"]);

        $this->from("/")
            ->post("/admin/quizzes", ["title" => "correct", "scheduled_at" => "invalid format"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->post("/admin/quizzes", ["title" => "correct", "scheduled_at" => "2022-01-01 01:01:01"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->post("/admin/quizzes", ["title" => "correct", "duration" => -100])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->from("/")
            ->post("/admin/quizzes", ["title" => "correct", "duration" => 0])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

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
        $quiz = Quiz::factory()->create(["title" => "Old quiz", "is_public" => false, "scheduled_at" => "2024-02-10 11:40:00"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);

        $data = [
            "title" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "is_public" => true,
            "description" => "test",
            "questions" => [
                [
                    "id" => $question->id,
                    "text" => "Question's content 1",
                ],
            ],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertRedirect("/");

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "title" => "Quiz Name",
            "description" => "test",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "is_public" => true,
        ]);

        $this->assertDatabaseHas("questions", [
            "id" => $question->id,
            "quiz_id" => $quiz->id,
            "text" => "Question's content 1",
        ]);
    }

    public function testAdminCanDeleteQuestion(): void
    {
        $quiz = Quiz::factory()->create(["title" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);
        $questions = Question::factory()->count(2)->create(["quiz_id" => $quiz->id]);

        $data = [
            "title" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "questions" => [],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertRedirect("/");

        $this->assertDatabaseCount("questions", 0);
    }

    public function testAdminCannotEditQuizWithQuestionThatHasMoreThanOneCorrectAnswer(): void
    {
        $quiz = Quiz::factory()->create(["title" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);

        $data = [
            "title" => "Quiz Name",
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

    public function testAdminCannotEditLocalQuizQuestions(): void
    {
        $quiz = Quiz::factory()->local()->has(Question::factory()->locked())->create(["title" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);
        $this->assertDatabaseCount("questions", 2);

        $data = [
            "title" => "Quiz Name",
            "scheduled_at" => "2024-08-28 15:00:00",
            "duration" => 120,
            "questions" => [],
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", $data)
            ->assertRedirect("/");

        $this->assertDatabaseCount("questions", 2);
    }

    public function testAdminCannotEditQuizThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/1", ["title" => "New quiz"])
            ->assertStatus(404);
    }

    public function testAdminCannotMakeInvalidEdit(): void
    {
        $quiz = Quiz::factory()->create(["title" => "Old quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", [])
            ->assertRedirect("/")->assertSessionHasErrors(["title"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["title" => true])
            ->assertRedirect("/")->assertSessionHasErrors(["title"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["title" => "correct", "scheduled_at" => "invalid format"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["title" => "correct", "scheduled_at" => "2022-01-01 01:01:01"])
            ->assertRedirect("/")->assertSessionHasErrors(["scheduled_at"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["title" => "correct", "duration" => -100])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["title" => "correct", "duration" => 0])
            ->assertRedirect("/")->assertSessionHasErrors(["duration"]);

        $this->assertDatabaseHas("quizzes", ["title" => "Old quiz"]);
    }

    public function testAdminCannotEditLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["title" => "Old quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/quizzes/{$quiz->id}", ["title" => "New quiz"])
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["title" => "Old quiz"]);
    }

    public function testAdminCanDeleteQuiz(): void
    {
        $quiz = Quiz::factory()->create(["title" => "quiz"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        Answer::factory()->create(["question_id" => $question->id]);

        $this->assertDatabaseCount("quizzes", 1);
        $this->assertDatabaseCount("questions", 1);
        $this->assertDatabaseCount("answers", 1);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("quizzes", ["title" => "quiz"]);
        $this->assertDatabaseCount("quizzes", 0);
        $this->assertDatabaseCount("questions", 0);
        $this->assertDatabaseCount("answers", 0);
    }

    public function testAdminCannotDeleteLockedQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create(["title" => "quiz"]);

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/quizzes/{$quiz->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("quizzes", ["title" => "quiz"]);
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
        $quiz = Quiz::factory()
            ->has(Question::factory()->locked())
            ->create(["scheduled_at" => Carbon::now()->addDay(), "duration" => 30, "locked_at" => null]);

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
        $quiz->duration = 30;
        $quiz->scheduled_at = Carbon::now()->addDay();
        $quiz->locked_at = null;
        $quiz->save();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("admin/quizzes/{$quiz->id}/lock")
            ->assertStatus(403);
    }

    public function testAdminCanLockLocalQuizThatHasQuestionsWithoutCorrectAnswer(): void
    {
        $answer = Answer::factory()->locked()->create();
        $answer->question->correct_answer_id = null;
        $answer->question->save();

        $quiz = $answer->question->quiz;
        $quiz->duration = 30;
        $quiz->scheduled_at = Carbon::now()->addDay();
        $quiz->locked_at = null;
        $quiz->is_local = true;
        $quiz->save();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/lock")
            ->assertRedirect("/")
            ->assertSessionHas(["status" => "Test oznaczony jako gotowy do publikacji"]);
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

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/unlock")
            ->assertStatus(403);
    }

    public function testAdminCannotUnlockQuizWithPastScheduledTime(): void
    {
        $quiz = Quiz::factory()->locked()->create([
            "scheduled_at" => Carbon::now()->subMinutes(60),
        ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/quizzes/{$quiz->id}/unlock")
            ->assertStatus(403);
    }

    public function testAdminCanSetUnlockedQuizToLocalAndOnline(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$quiz->id}/local")
            ->assertRedirect("/")
            ->assertSessionHas(["status" => "Tryb testu został zmieniony na stacjonarny."]);

        $this->assertDatabaseHas("quizzes", [
            "is_local" => true,
            "id" => $quiz->id,
        ]);

        $this->actingAs($this->admin)
            ->post("/admin/quizzes/{$quiz->id}/online")
            ->assertRedirect("/")
            ->assertSessionHas(["status" => "Tryb testu został zmieniony na zdalny."]);

        $this->assertDatabaseHas("quizzes", [
            "is_local" => false,
            "id" => $quiz->id,
        ]);
    }

    public function testUserCanStartQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->has(Question::factory()->locked())->create();

        $response = $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start");

        $userQuiz = UserQuiz::query()->where([
            "user_id" => $this->user->id,
            "quiz_id" => $quiz->id,
        ])->firstOrFail();

        $response->assertRedirect("/quizzes/$userQuiz->id/");
    }

    public function testUninvitedUserCannotStartPrivateQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->private()->create([
            "scheduled_at" => Carbon::now()->subMinutes(60),
        ]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start")
            ->assertStatus(403);

        $this->assertDatabaseCount("user_quizzes", 0);
    }

    public function testInvitedUserCanStartPrivateQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->private()->create([
            "scheduled_at" => Carbon::now()->subMinutes(60),
        ]);

        $quiz->assignedUsers()->attach($this->user);

        $response = $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start");

        $userQuiz = UserQuiz::query()->where([
            "user_id" => $this->user->id,
            "quiz_id" => $quiz->id,
        ])->firstOrFail();

        $response->assertRedirect("/quizzes/$userQuiz->id/");
    }

    public function testInvitedUserCanViewPublicQuiz(): void
    {
        Quiz::factory()->locked()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->get("/dashboard")
            ->assertInertia(
                fn(Assert $page) => $page->component("User/Dashboard")
                    ->has("quizzes", 1),
            );
    }

    public function testInvitedUserCanViewPrivateQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->private()->create();
        $quiz->assignedUsers()->attach($this->user);

        $this->actingAs($this->user)
            ->from("/")
            ->get("/dashboard")
            ->assertInertia(
                fn(Assert $page) => $page->component("User/Dashboard")
                    ->has("quizzes", 1),
            );
    }

    public function testUninvitedUserCannotViewPrivateQuiz(): void
    {
        Quiz::factory()->private()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->get("/dashboard")
            ->assertInertia(
                fn(Assert $page) => $page->component("User/Dashboard")
                    ->has("quizzes", 0),
            );
    }

    public function testUserCannotStartAlreadyStartedQuiz(): void
    {
        $userQuiz = UserQuiz::factory()->create(["user_id" => $this->user->id]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$userQuiz->quiz->id}/start")
            ->assertRedirect("/quizzes/{$userQuiz->id}/");

        $this->assertDatabaseCount("user_quizzes", 1);
    }

    public function testUserCannotStartLocalQuiz(): void
    {
        $quiz = Quiz::factory()->local()->locked()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start")
            ->assertStatus(403);

        $this->assertDatabaseCount("user_quizzes", 0);
    }

    public function testUserCannotStartUnlockedQuiz(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/start")
            ->assertStatus(403);

        $this->assertDatabaseCount("user_quizzes", 0);
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

    public function testUserCanAssignThemselvesToQuiz(): void
    {
        $quiz = Quiz::factory()->locked()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/assign")
            ->assertRedirect("/")
            ->assertSessionHas(["status" => "Przypisano do testu"]);
    }

    public function testUserCannotAssignThemselvesToUnlockedQuiz(): void
    {
        $quiz = Quiz::factory()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/assign")
            ->assertStatus(403);
    }

    public function testUserCannotAssignThemselvesToQuizWithPastScheduledTime(): void
    {
        $quiz = Quiz::factory()->locked()->create([
            "scheduled_at" => Carbon::now()->subMinutes(60),
        ]);

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/assign")
            ->assertStatus(403);
    }

    public function testUserCannotAssignThemselvesToStartedQuiz(): void
    {
        $quiz = UserQuiz::factory()->create(["user_id" => $this->user->id])->quiz;

        $this->actingAs($this->user)
            ->from("/")
            ->post("/quizzes/{$quiz->id}/assign")
            ->assertStatus(403);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerRecordTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanAnswerQuestion(): void
    {
        $answer = Answer::factory()->locked()->create();
        $submission = $answer->question->quiz->createSubmission($this->user);
        $record = $submission->answerRecords[0];

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/answers/{$record->id}/{$answer->id}")
            ->assertRedirect("/");

        $this->assertDatabaseHas("answer_records", [
            "id" => $record->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionThatNotExisted(): void
    {
        $answer = Answer::factory()->locked()->create();

        $this->actingAs($this->user)
            ->patch("/answers/0/{$answer->id}")
            ->assertStatus(404);
    }

    public function testUserCannotAnswerQuestionThatIsNotTheirs(): void
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->locked()->create();
        $submission = $answer->question->quiz->createSubmission($user);
        $record = $submission->answerRecords[0];

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/answers/{$record->id}/{$answer->id}")
            ->assertStatus(403);

        $this->assertDatabaseMissing("answer_records", [
            "id" => $record->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionThatBelongsToClosedSubmission(): void
    {
        $answer = Answer::factory()->locked()->create();
        $submission = $answer->question->quiz->createSubmission($this->user);
        $record = $submission->answerRecords[0];

        $submission->closed_at = Carbon::now();
        $submission->save();

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/answers/{$record->id}/{$answer->id}")
            ->assertStatus(403);

        $this->assertDatabaseMissing("answer_records", [
            "id" => $record->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionWithAnswerThatNotExist(): void
    {
        $answer = Answer::factory()->locked()->create();
        $submission = $answer->question->quiz->createSubmission($this->user);
        $record = $submission->answerRecords[0];

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/ansers/{$submission->id}/4}")
            ->assertStatus(404);

        $this->assertDatabaseMissing("answer_records", [
            "id" => $record->id,
            "answer_id" => $answer->id,
        ]);
    }

    public function testUserCannotAnswerQuestionWithAnswerNotAssignedToIt(): void
    {
        $answer = Answer::factory()->locked()->create();
        $submission = $answer->question->quiz->createSubmission($this->user);
        $record = $submission->answerRecords[0];

        $answer1 = Answer::factory()->locked()->create();

        $this->actingAs($this->user)
            ->from("/")
            ->patch("/answers/{$submission->id}/{$answer1->id}")
            ->assertStatus(403);

        $this->assertDatabaseMissing("answer_records", [
            "id" => $record->id,
            "answer_id" => $answer1->id,
        ]);
    }
}

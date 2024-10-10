<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\QuizUpdateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QuizUpdateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10));

        $this->service = new QuizUpdateService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testQuizEditing(): void
    {
        $quiz = Quiz::factory()->create(["title" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);
        $answer = Answer::factory()->create(["question_id" => $question->id]);

        $data = [
            "title" => "Quiz Name",
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

        $this->service->update($quiz, $data);

        $this->assertDatabaseHas("quizzes", [
            "id" => $quiz->id,
            "title" => "Quiz Name",
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

    public function testQuizEditingWithNonExistentAnswer(): void
    {
        $quiz = Quiz::factory()->create(["title" => "Old quiz", "scheduled_at" => "2024-02-10 11:40:00"]);
        $question = Question::factory()->create(["quiz_id" => $quiz->id]);

        $data = [
            "title" => "Quiz Name",
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

        $this->expectException(ModelNotFoundException::class);
        $this->service->update($quiz, $data);
    }

    public function testQuizEditingWithNonExistentQuestion(): void
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
                ],
            ],
        ];

        $this->expectException(ModelNotFoundException::class);
        $this->service->update($quiz, $data);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Question;
use App\Models\Quiz;

class QuizUpdateService
{
    public function update(Quiz $quiz, array $data): void
    {
        $quiz->fill($data);

        if (array_key_exists("questions", $data)) {
            $questions = collect($data["questions"]);
            $quiz->questions()->whereNotIn("id", $questions->pluck("id")->whereNotNull())->delete();

            foreach ($data["questions"] as $questionData) {
                $question = $quiz->questions()->updateOrCreate(
                    ["id" => $questionData["id"] ?? null],
                    $questionData,
                );

                $this->updateQuestion($question, $questionData);
            }
        }

        $quiz->save();
    }

    protected function updateQuestion(Question $question, array $data): void
    {
        $question->fill($data);

        if (array_key_exists("answers", $data)) {
            $answers = collect($data["answers"])->filter(fn($answer) => isset($answer["text"]) && trim($answer["text"]) !== "");
            $question->answers()->whereNotIn("id", $answers->pluck("id")->whereNotNull())->delete();

            foreach ($answers as $answerData) {
                $answer = $question->answers()->updateOrCreate(
                    ["id" => $answerData["id"] ?? null],
                    $answerData,
                );

                if (array_key_exists("correct", $answerData) && $answerData["correct"] === true) {
                    $question->correctAnswer()->associate($answer);
                }

                $answer->update($answerData);
            }
        }

        $question->save();
    }
}

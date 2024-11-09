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
                $question = null;

                if (array_key_exists("id", $questionData)) {
                    $question = $quiz->questions()->findOrFail($questionData["id"]);
                } else {
                    $question = $quiz->questions()->create($questionData);
                }

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
                $answer = null;

                if (array_key_exists("id", $answerData)) {
                    $answer = $question->answers()->findOrFail($answerData["id"]);
                } else {
                    $answer = $question->answers()->create($answerData);
                }

                if (array_key_exists("correct", $answerData) && $answerData["correct"] === true) {
                    $question->correctAnswer()->associate($answer);
                }

                $answer->update($answerData);
            }
        }

        $question->save();
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if ($this->has("scheduledAt")) {
            $this->merge(["scheduled_at" => $this->input("scheduledAt")]);
        }
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => ["required", "string"],
            "scheduled_at" => ["date", "after:now"],
            "duration" => ["integer", "min:1"],
            "questions" => ["array"],
            "questions.*.id" => "integer|min:0",
            "questions.*.text" => "required|string",
            "questions.*.answers" => "array",
            "questions.*.answers.*.id" => "integer|min:0",
            "questions.*.answers.*.text" => "required|string",
            "questions.*.answers.*.correct" => "boolean",
        ];
    }

    /**
     * @throws ValidationException
     */
    protected function passedValidation(): void
    {
        $validatedData = $this->validated();

        if (array_key_exists("questions", $validatedData)) {
            foreach ($validatedData["questions"] as $question) {
                $this->validateCorrectAnswer($question);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    protected function validateCorrectAnswer(array $question): void
    {
        if (!array_key_exists("answers", $question)) {
            return;
        }

        $correctAnswers = array_filter($question["answers"], fn(array $answer): bool => array_key_exists("correct", $answer) && $answer["correct"]);

        if (count($correctAnswers) > 1) {
            throw ValidationException::withMessages([
                "questions" => "Każde pytanie może mieć maksymalnie jedną poprawną odpowiedź.",
            ]);
        }
    }
}

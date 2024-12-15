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

        if ($this->has("isPublic")) {
            $this->merge(["is_public" => $this->input("isPublic")]);
        }
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => ["required", "string", "max:255"],
            "scheduled_at" => ["date", "after:now"],
            "is_public" => ["boolean"],
            "duration" => ["integer", "min:1", "max:2147483647"],
            "description" => ["string", "nullable"],
            "questions" => ["array"],
            "questions.*.id" => ["integer", "min:0"],
            "questions.*.text" => ["required", "string"],
            "questions.*.answers" => ["array"],
            "questions.*.answers.*.id" => ["integer", "min:0"],
            "questions.*.answers.*.text" => ["nullable", "string"],
            "questions.*.answers.*.correct" => ["boolean"],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "scheduled_at.after" => "Pole :attribute musi być ustawione na datę późniejszą niż obecna.",
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

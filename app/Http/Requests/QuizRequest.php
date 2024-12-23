<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
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
            "duration" => ["numeric", "min:1", "max:2147483647"],
            "description" => ["string", "nullable"],
            "is_public" => ["boolean"],
        ];
    }
}

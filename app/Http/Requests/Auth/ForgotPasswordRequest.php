<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "email" => "required|string|email:dns|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            "email" => [
                "required" => "The email field is required.",
                "max" => "Your email is too long. It must not be greater than 255 characters.",
                "email" => "Your email is invalid.",
            ],
        ];
    }
}

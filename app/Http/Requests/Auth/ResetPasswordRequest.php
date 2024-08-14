<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            "token" => "required",
            "email" => "required|email|max:255",
            "password" => "required|min:8|confirmed:password_confirmation",
        ];
    }

    public function messages(): array
    {
        return [
            "token" => [
                "required" => "The token field is required.",
            ],
            "email" => [
                "required" => "The email field is required.",
                "max" => "Your email is too long. It must not be greater than 255 characters.",
                "email" => "Your email is invalid.",
            ],
            "password" => [
                "required" => "The password field is required.",
                "min" => "Your password is too short. It must be at least 8 characters.",
                "confirmed" => "The password field confirmation does not match.",
            ],
        ];
    }
}

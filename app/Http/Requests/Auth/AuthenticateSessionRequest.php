<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticateSessionRequest extends FormRequest
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
            "email" => "required|email|max:255",
            "password" => "required|string",
        ];
    }

    public function messages(): array
    {
        return [
            "email" => [
                "required" => "The email field is required.",
                "max" => "Your email is too long. It must not be greater than 255 characters.",
                "unique" => "The email has already been taken.",
                "email" => "Your email is invalid.",
            ],

            "password" => [
                "required" => "The password field is required.",
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            "email" => "required|string|email:rfc,dns|max:255|unique:" . User::class,
            "name" => "required|string|max:255",
            "surname" => "required|string|max:255",
            "password" => "required|string|min:8",
            "school_id" => "required|integer|exists:schools,id",
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
            "name" => [
                "required" => "The name field is required",
                "max" => "Your name is too long. It must not be greater than 255 characters.",
            ],
            "surname" => [
                "required" => "The surname field is required",
                "max" => "Your surname is too long. It must not be greater than 255 characters.",
            ],
            "password" => [
                "required" => "The password field is required.",
                "min" => "Your password is too short. It must be at least 8 characters.",
            ],
            "school_id" => [
                "required" => "The school field is required.",
                "exists" => "Your school is invalid. Check it again.",
            ],
        ];
    }
}

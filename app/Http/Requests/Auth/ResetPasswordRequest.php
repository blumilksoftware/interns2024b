<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "token" => ["required"],
            "email" => ["required", "email", "max:255"],
            "password" => ["required", "min:8", "confirmed:password_confirmation"],
        ];
    }
}
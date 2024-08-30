<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ProfileUserPasswordResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "current_password" => ["required", "current_password"],
            "password" => ["required", Password::defaults(), "confirmed"],
        ];
    }
}

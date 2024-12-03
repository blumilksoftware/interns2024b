<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => ["required", "string", "email:rfc,dns", "max:255"],
            "firstname" => ["required", "string", "max:255"],
            "surname" => ["required", "string", "max:255"],
            "password" => ["required", "string", "min:8"],
            "school_id" => ["required", "integer", "exists:schools,id"],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => ["required", "string", "email", "max:255", "unique:users,email," . $this->user->id],
            "firstname" => ["required", "string", "max:255"],
            "surname" => ["required", "string", "max:255"],
            "school_id" => ["required", "integer", "exists:schools,id"],
        ];
    }
}

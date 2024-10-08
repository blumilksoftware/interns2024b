<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\DateFormatHelper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
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
            "name" => ["required", "string"],
            "scheduled_at" => ["date", "date_format:" . DateFormatHelper::DATETIME_FORMAT, "after:now"],
            "duration" => ["integer", "min:1"],
        ];
    }
}

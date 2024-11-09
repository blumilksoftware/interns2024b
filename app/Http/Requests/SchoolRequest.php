<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if ($this->has("buildingNumber")) {
            $this->merge(["building_number" => $this->input("buildingNumber")]);
        }

        if ($this->has("apartmentNumber")) {
            $this->merge(["apartment_number" => $this->input("apartmentNumber")]);
        }

        if ($this->has("zipCode")) {
            $this->merge(["zip_code" => $this->input("zipCode")]);
        }
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string"],
            "city" => ["required", "string"],
            "street" => ["required", "string"],
            "building_number" => ["required", "string"],
            "apartment_number" => ["string"],
            "zip_code" => ["required", "string"],
        ];
    }
}

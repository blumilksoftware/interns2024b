<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "regon" => $this->regon,
            "city" => $this->city,
            "street" => $this->street,
            "isDisabled" => $this->is_disabled,
            "buildingNumber" => $this->building_number,
            "apartmentNumber" => $this->apartment_number,
            "zipCode" => $this->zip_code,
            "numberOfStudents" => $this->users()->count(),
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];
    }
}

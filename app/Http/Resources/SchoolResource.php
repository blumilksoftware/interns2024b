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
            "city" => $this->city,
            "street" => $this->street,
            "building" => $this->building_number,
            "apartment" => $this->apartment_number,
            "zipCode" => $this->zip_code,
        ];
    }
}

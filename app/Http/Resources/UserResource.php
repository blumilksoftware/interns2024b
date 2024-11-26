<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "surname" => $this->surname,
            "email" => $this->email,
            "school" => SchoolResource::make($this->school),
            "isAnonymized" => $this->is_anonymized,
            "isAdmin" => $this->hasRole("admin"),
            "isSuperAdmin" => $this->hasRole("super_admin"),
            "theme" => $this->theme,
        ];
    }
}

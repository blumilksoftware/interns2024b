<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InviteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user" => [
                "id" => $this->user->id,
                "name" => $this->user->name,
                "surname" => $this->user->surname,
                "school" => $this->user->school,
            ],
            "points" => $this->points,
        ];
    }
}

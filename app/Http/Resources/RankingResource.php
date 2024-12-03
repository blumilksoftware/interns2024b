<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()->hasRole("admin|super_admin");

        return [
            "user" => [
                "id" => $this->user->id,
                "firstname" => $isAdmin ? $this->user->firstname : null,
                "surname" => $isAdmin ? $this->user->surname : null,
                "school" => $this->user->school,
            ],
            "points" => $this->points,
        ];
    }
}

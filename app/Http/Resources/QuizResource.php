<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "scheduledAt" => $this->scheduled_at,
            "name" => $this->name,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "scheduledUntil" => $this->scheduled_until,
            "locked" => $this->isLocked,
            "questions" => QuestionResource::collection($this->questions),
        ];
    }
}

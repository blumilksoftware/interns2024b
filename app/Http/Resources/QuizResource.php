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
            "title" => $this->title,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "scheduledAt" => $this->scheduled_at,
            "duration" => $this->duration,
            "state" => $this->state,
            "canBeLocked" => $this->canBeLocked,
            "canBeUnlocked" => $this->canBeUnlocked,
            "questions" => QuestionResource::collection($this->questions),
            "isUserAssigned" => $this->isUserAssigned($request->user()),
            "isRankingPublished" => $this->isRankingPublished,
        ];
    }
}

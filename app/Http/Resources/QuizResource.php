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
            "name" => $this->name,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "duration" => $this->duration,
            "locked" => $this->isLocked,
            "questions" => QuestionResource::collection($this->questions),
        ];
    }
}

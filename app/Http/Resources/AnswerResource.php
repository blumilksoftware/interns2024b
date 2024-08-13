<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "text" => $this->text,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "locked" => $this->isLocked,
            "correct" => $this->isCorrect,
        ];
    }
}

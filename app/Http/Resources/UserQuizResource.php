<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserQuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->quiz->title,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "closedAt" => $this->closed_at,
            "closed" => $this->isClosed,
            "quiz" => $this->quiz_id,
            "questions" => UserQuestionResource::collection($this->userQuestions->shuffle()),
        ];
    }
}

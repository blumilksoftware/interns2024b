<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->quiz->title,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "closedAt" => $this->closed_at,
            "closed" => $this->isClosed,
            "answers" => AnswerRecordResource::collection($this->answerRecords->shuffle()),
        ];
    }
}

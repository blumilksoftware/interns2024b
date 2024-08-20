<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $answers = collect();

        foreach ($this->question->answers as $answer) {
            $answers->add([
                "id" => $answer->id,
                "text" => $answer->text,
            ]);
        }

        return [
            "id" => $this->id,
            "question" => $this->question->text,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "closed" => $this->isClosed,
            "answers" => $answers->shuffle(),
            "selected" => $this->answer_id,
        ];
    }
}

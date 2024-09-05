<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AnswerRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "question" => $this->question->text,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "closed" => $this->isClosed,
            "selected" => $this->answer_id,
            "answers" => $this->questionAnswersToArray($this->question)->shuffle(),
        ];
    }

    /**
     * @return Collection<array>
     */
    protected function questionAnswersToArray(Question $question): Collection
    {
        if ($question->quiz->isRankingPublished) {
            return $question->answers->map(fn(Answer $answer) => $this->getFullAnswer($answer))->collect();
        }

        return $question->answers->map(fn(Answer $answer) => $this->getMinimalAnswer($answer))->collect();
    }

    protected function getFullAnswer(Answer $answer): array
    {
        return [
            "id" => $answer->id,
            "text" => $answer->text,
            "correct" => $answer->isCorrect,
        ];
    }

    protected function getMinimalAnswer(Answer $answer): array
    {
        return [
            "id" => $answer->id,
            "text" => $answer->text,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $quiz_submission_id
 * @property int $question_id
 * @property int $answer_id
 * @property bool $isClosed
 * @property bool $isCorrect
 * @property QuizSubmission $quizSubmission
 * @property Question $question
 * @property Answer $answer
 */
class AnswerRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        "text",
    ];

    public function quizSubmission(): BelongsTo
    {
        return $this->belongsTo(QuizSubmission::class);
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function isClosed(): Attribute
    {
        return Attribute::get(fn(): bool => $this->quizSubmission->isClosed);
    }

    public function isCorrect(): Attribute
    {
        return Attribute::get(fn(): bool => $this->answer?->isCorrect ?? false);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $user_quiz_id
 * @property int $question_id
 * @property int $answer_id
 * @property bool $isClosed
 * @property bool $isCorrect
 * @property UserQuiz $userQuiz
 * @property Question $question
 * @property ?Disqualification $disqualification
 * @property ?Answer $answer
 */
class UserQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        "text",
    ];

    public function userQuiz(): BelongsTo
    {
        return $this->belongsTo(UserQuiz::class);
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function disqualifications(): HasOne
    {
        return $this->hasOne(Disqualification::class);
    }

    public function isClosed(): Attribute
    {
        return Attribute::get(fn(): bool => $this->userQuiz->isClosed);
    }

    public function isCorrect(): Attribute
    {
        return Attribute::get(fn(): bool => $this->answer->isCorrect ?? false);
    }
}

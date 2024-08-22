<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $closed_at
 * @property int $quiz_id
 * @property int $user_id
 * @property bool $isClosed
 * @property Quiz $quiz
 * @property User $user
 * @property Collection<AnswerRecord> $answerRecords
 */
class QuizSubmission extends Model
{
    use HasFactory;

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answerRecords(): HasMany
    {
        return $this->hasMany(AnswerRecord::class);
    }

    public function isClosed(): Attribute
    {
        return Attribute::get(fn(): bool => $this->closed_at !== null && $this->closed_at <= Carbon::now());
    }

    protected function casts(): array
    {
        return [
            "closed_at" => "datetime",
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $closed_at
 * @property int $quiz_id
 * @property int $user_id
 * @property int $points
 * @property int $maxPoints
 * @property bool $isClosed
 * @property Quiz $quiz
 * @property User $user
 * @property ?Disqualification $disqualification
 * @property Collection<UserQuestion> $userQuestions
 */
class UserQuiz extends Model
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

    public function disqualification(): HasOne
    {
        return $this->hasOne(Disqualification::class);
    }

    public function userQuestions(): HasMany
    {
        return $this->hasMany(UserQuestion::class);
    }

    public function isClosed(): Attribute
    {
        return Attribute::get(fn(): bool => $this->closed_at <= Carbon::now());
    }

    public function wasClosedManually(): bool
    {
        return $this->closed_at->ne($this->quiz->closeAt);
    }

    public function points(): Attribute
    {
        return Attribute::get(function (): int {
            $correctAnswers = $this->userQuestions->filter(fn(UserQuestion $userQuestion): bool => $userQuestion->isCorrect);

            return $correctAnswers->count();
        });
    }

    public function maxPoints(): Attribute
    {
        return Attribute::get(fn(): int => $this->quiz->questions()->count());
    }

    protected function casts(): array
    {
        return [
            "closed_at" => "datetime",
        ];
    }
}

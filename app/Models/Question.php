<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $text
 * @property int $test_id
 * @property int $correct_answer_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property bool $isLocked
 *
 * @property ?Answer $correctAnswer
 * @property Collection<Answer> $answers
 * @property Test $test
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        "text",
    ];

    public function correctAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, "correct_answer_id");
    }

    protected function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    protected function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    protected function isLocked(): Attribute
    {
        return Attribute::get(fn(): bool => $this->test->isLocked);
    }
}

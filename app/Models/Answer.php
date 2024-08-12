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
 * @property string $text
 * @property int $question_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property bool $isLocked
 * @property bool $isCorrect
 *
 * @property Question $question
 */
class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        "text",
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function isLocked(): Attribute
    {
        return Attribute::get(fn(): bool => $this->question->isLocked);
    }

    public function isCorrect(): Attribute
    {
        return Attribute::get(fn(): bool => $this->question->correctAnswer()->is($this));
    }

    public function cloneTo(Question $question): self
    {
        $clone = $this->replicate();
        $clone->question()->associate($question)->save();

        return $clone;
    }
}

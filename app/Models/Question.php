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
 * @property bool $isLocked
 * @property bool $hasCorrectAnswer
 * @property ?Answer $correctAnswer
 * @property Collection<Answer> $answers
 * @property Quiz $quiz
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

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isLocked(): Attribute
    {
        return Attribute::get(fn(): bool => $this->quiz->isLocked);
    }

    public function hasCorrectAnswer(): Attribute
    {
        return Attribute::get(fn(): bool => $this->correct_answer_id !== null);
    }

    public function cloneTo(Quiz $quiz): self
    {
        $questionCopy = $this->replicate();
        $questionCopy->quiz()->associate($quiz)->save();

        foreach ($this->answers as $answer) {
            $answerCopy = $answer->cloneTo($questionCopy);

            if ($answer->isCorrect) {
                $questionCopy->correctAnswer()->associate($answerCopy);
            }
        }

        $questionCopy->save();

        return $questionCopy;
    }
}

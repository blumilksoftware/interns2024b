<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $locked_at
 *
 * @property bool $isLocked
 *
 * @property Collection<Question> $questions
 * @property Collection<Answer> $answers
 */
class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasManyThrough
    {
        return $this->hasManyThrough(Answer::class, Question::class);
    }

    public function isLocked(): Attribute
    {
        return Attribute::get(fn(): bool => $this->locked_at !== null);
    }

    /**
     * @throws AuthorizationException
     */
    public function clone(): self
    {
        $quizCopy = $this->replicate();
        $quizCopy->save();

        foreach ($this->questions as $question) {
            $question->cloneTo($quizCopy);
        }

        return $quizCopy;
    }

    protected function casts(): array
    {
        return [
            "locked_at" => "datetime",
        ];
    }
}

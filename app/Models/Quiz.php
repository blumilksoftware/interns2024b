<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
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
 * @property Carbon $scheduled_at
 * @property Carbon $ranking_published_at
 * @property ?Carbon $locked_at
 * @property ?int $duration
 * @property bool $isLocked
 * @property bool $canBeLocked
 * @property bool $canBeUnlocked
 * @property bool $isRankingPublished
 * @property ?Carbon $closeAt
 * @property Collection<Question> $questions
 * @property Collection<Answer> $answers
 */
class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "scheduled_at",
        "duration",
        "ranking_published_at",
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

    public function isRankingPublished(): Attribute
    {
        return Attribute::get(fn(): bool => $this->ranking_published_at !== null);
    }

    public function canBeUnlocked(): Attribute
    {
        return Attribute::get(fn(): bool => $this->isLocked && $this->scheduled_at > Carbon::now());
    }

    public function canBeLocked(): Attribute
    {
        return Attribute::get(fn(): bool => !$this->isLocked && $this->isReadyToBePublished() && $this->scheduled_at > Carbon::now());
    }

    public function closeAt(): Attribute
    {
        return Attribute::get(fn(): ?Carbon => $this->isReadyToBePublished() ? $this->scheduled_at->copy()->addMinutes($this->duration) : null);
    }

    public function clone(): self
    {
        $quizCopy = $this->replicate();
        $quizCopy->save();

        foreach ($this->questions as $question) {
            $question->cloneTo($quizCopy);
        }

        return $quizCopy;
    }

    public function createSubmission(User $user): QuizSubmission
    {
        $submission = new QuizSubmission();
        $submission->closed_at = $this->closeAt;
        $submission->quiz()->associate($this);
        $submission->user()->associate($user);
        $submission->save();

        foreach ($this->questions as $question) {
            $answerRecord = new AnswerRecord();
            $answerRecord->quizSubmission()->associate($submission);
            $answerRecord->question()->associate($question);
            $answerRecord->save();
        }

        return $submission;
    }

    public function isReadyToBePublished(): bool
    {
        return $this->scheduled_at !== null && $this->duration !== null && $this->allQuestionsHaveCorrectAnswer();
    }

    protected function allQuestionsHaveCorrectAnswer(): bool
    {
        return $this->questions->every(fn(Question $question): bool => $question->hasCorrectAnswer);
    }

    public function publishRanking(): void
    {
        $this->ranking_published_at = Carbon::now();
        $this->save();
    }

    public function unpublishRanking(): void
    {
        $this->ranking_published_at = null;
        $this->save();
    }

    protected function casts(): array
    {
        return [
            "scheduled_at" => "datetime",
        ];
    }
}

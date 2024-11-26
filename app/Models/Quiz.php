<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $scheduled_at
 * @property Carbon $ranking_published_at
 * @property ?Carbon $locked_at
 * @property ?int $duration
 * @property bool $isLocked
 * @property bool $isPublished
 * @property bool $canBeLocked
 * @property bool $canBeUnlocked
 * @property string $state
 * @property bool $isRankingPublished
 * @property ?Carbon $closeAt
 * @property Collection<Question> $questions
 * @property Collection<Answer> $answers
 * @property Collection<User> $assignedUsers
 * @property Collection<UserQuiz> $userQuizzes
 */
class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "scheduled_at",
        "duration",
        "ranking_published_at",
    ];
    protected $guarded = [];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasManyThrough
    {
        return $this->hasManyThrough(Answer::class, Question::class);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "quiz_assignments");
    }

    public function userQuizzes(): HasMany
    {
        return $this->hasMany(UserQuiz::class);
    }

    public function isLocked(): Attribute
    {
        return Attribute::get(fn(): bool => $this->locked_at !== null);
    }

    public function isPublished(): Attribute
    {
        return Attribute::get(fn(): bool => $this->isLocked && !$this->canBeUnlocked);
    }

    public function state(): Attribute
    {
        return Attribute::get(
            fn(): string => $this->isPublished ? "published" : ($this->isLocked ? "locked" : "unlocked"),
        );
    }

    public function isUserAssigned(User $user): bool
    {
        return $this->assignedUsers->contains($user);
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
        $quizCopy->title = $quizCopy->title . " - kopia";
        $quizCopy->locked_at = null;
        $quizCopy->duration = null;
        $quizCopy->scheduled_at = null;
        $quizCopy->save();

        foreach ($this->questions as $question) {
            $question->cloneTo($quizCopy);
        }

        return $quizCopy;
    }

    public function createUserQuiz(User $user): UserQuiz
    {
        $userQuiz = new UserQuiz();
        $userQuiz->closed_at = $this->closeAt;
        $userQuiz->quiz()->associate($this);
        $userQuiz->user()->associate($user);
        $userQuiz->save();

        foreach ($this->questions as $question) {
            $userQuestion = new UserQuestion();
            $userQuestion->userQuiz()->associate($userQuiz);
            $userQuestion->question()->associate($question);
            $userQuestion->save();
        }

        return $userQuiz;
    }

    public function isReadyToBePublished(): bool
    {
        return $this->scheduled_at !== null && $this->duration !== null && $this->allQuestionsHaveCorrectAnswer();
    }

    public function hasUserQuizzesFrom(User $user): bool
    {
        return $this->userQuizzes->where("user_id", $user->id)->isNotEmpty();
    }

    protected function allQuestionsHaveCorrectAnswer(): bool
    {
        return $this->questions->every(fn(Question $question): bool => $question->hasCorrectAnswer);
    }

    protected function casts(): array
    {
        return [
            "scheduled_at" => "datetime",
        ];
    }
}

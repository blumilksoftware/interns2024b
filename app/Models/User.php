<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\SendVerificationEmail;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @param string $token
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $password
 * @property Carbon $email_verified_at
 * @property int $school_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property School $school
 * @property boolean $is_anonymized
 * @property Collection<QuizSubmission> $quizSubmissions
 * @property Collection<Quiz> $assignedQuizzes
 */
class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        "name",
        "surname",
        "email",
        "school_id",
        "is_anonymized",
    ];
    protected $hidden = [
        "remember_token",
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new SendVerificationEmail());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function quizSubmissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function assignedQuizzes()
    {
        return $this->belongsToMany(Quiz::class, "quiz_assignments");
    }

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }
}

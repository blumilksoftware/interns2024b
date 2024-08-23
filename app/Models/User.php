<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RolesEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $password
 * @property Carbon $email_verified_at
 * @property int $school_id
 * @property RolesEnum role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property School $school
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
    ];
    protected $hidden = [
        "remember_token",
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RolesEnum::ADMIN) || $this->hasRole(RolesEnum::SUPERADMIN);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RolesEnum::SUPERADMIN);
    }

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "role" => RolesEnum::class,
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $reason
 * @property int $user_quiz_id
 * @property UserQuiz $userQuiz
 */
class Disqualification extends Model
{
    use HasFactory;

    public function userQuiz(): BelongsTo
    {
        return $this->belongsTo(UserQuiz::class);
    }
}

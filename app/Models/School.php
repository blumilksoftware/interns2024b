<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $regon
 * @property string $name
 * @property string $city
 * @property string $street
 * @property string $building_number
 * @property string $apartment_number
 * @property string $zip_code
 * @property boolean $is_disabled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<User> $users
 */
class School extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "regon",
        "city",
        "street",
        "building_number",
        "apartment_number",
        "zip_code",
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

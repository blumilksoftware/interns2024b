<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $name
 * @property string $city
 * @property string $street
 * @property string $building_number
 * @property string $apartment_number
 * @property string $zipCode
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class School extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "city",
        "street",
        "building_number",
        "apartment_number",
        "zipCode",
        ];
}

<?php

declare(strict_types=1);

use App\Enums\SchoolType;
use App\Enums\Voivodeship;

return [
    "voivodeships" => [
        Voivodeship::LOWER_SILESIA,
    ],
    "types" => [
        SchoolType::LICEUM_OGOLNOKSZTALCACE,
        SchoolType::TECHNIKUM,
        SchoolType::LICEUM_UZUPELNIAJACE,
        SchoolType::BEDNARSKA_SZKOLA_REALNA,
        SchoolType::SZKOLA_BRANZOWA_DRUGIEGO_STOPNIA,
    ],
];

<?php

declare(strict_types=1);

namespace App\Enums;

enum SchoolType: int
{
    case LICEUM_OGOLNOKSZTALCACE = 14;
    case TECHNIKUM = 16;
    case LICEUM_UZUPELNIAJACE = 17;
    case BEDNARSKA_SZKOLA_REALNA = 90;
    case SZKOLA_BRANZOWA_DRUGIEGO_STOPNIA = 94;
}

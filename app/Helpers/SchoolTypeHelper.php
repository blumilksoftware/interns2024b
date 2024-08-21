<?php

declare(strict_types=1);

namespace App\Helpers;

class SchoolTypeHelper
{
    public const int LICEUM_OGOLNOKSZTALCACE = 14;
    public const int TECHNIKUM = 16;
    public const int LICEUM_UZUPELNIAJACE = 17;
    public const int BEDNARSKA_SZKOLA_REALNA = 90;
    public const int SZKOLA_BRANZOWA_DRUGIEGO_STOPNIA = 94;
    public const int ZESPOL_SZKOL_PLACOWEK_OSWIATOWYCH = 100;

    /**
     * @return array<int>
     */
    public static function all(): array
    {
        return [
            self::LICEUM_OGOLNOKSZTALCACE,
            self::TECHNIKUM,
            self::LICEUM_UZUPELNIAJACE,
            self::BEDNARSKA_SZKOLA_REALNA,
            self::SZKOLA_BRANZOWA_DRUGIEGO_STOPNIA,
            self::ZESPOL_SZKOL_PLACOWEK_OSWIATOWYCH,
        ];
    }
}

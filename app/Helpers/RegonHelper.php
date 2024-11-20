<?php

declare(strict_types=1);

namespace App\Helpers;

class RegonHelper
{
    public const array WEIGHTS_SHORT = [8, 9, 2, 3, 4, 5, 6, 7];
    public const array WEIGHTS_LONG = [2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8];

    public static function generateShortRegon()
    {
        $digits = "";

        for ($i = 0; $i < 8; $i++) {
            $digits .= rand(0, 9);
        }

        $checksum = self::calculateChecksum(str_split($digits), self::WEIGHTS_SHORT);

        return $digits . $checksum;
    }

    public static function generateLongRegon()
    {
        $digits = "";

        for ($i = 0; $i < 13; $i++) {
            $digits .= rand(0, 9);
        }

        $checksum = self::calculateChecksum(str_split($digits), self::WEIGHTS_LONG);

        return $digits . $checksum;
    }

    /***
     * @param array<string> $number
     * @param array<int> $wages
     */
    public static function calculateChecksum(array $number, array $wages): int
    {
        $sum = 0;

        for ($i = 0; $i < count($wages); $i++) {
            $sum += $wages[$i] * intval($number[$i]);
        }

        $checksum = $sum % 11;

        if ($checksum === 10) {
            return 0;
        }

        return $checksum;
    }
}

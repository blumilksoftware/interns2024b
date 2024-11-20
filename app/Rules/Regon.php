<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Lang;

use function ctype_digit;
use function intval;
use function str_split;
use function strlen;

class Regon implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ctype_digit($value)) {
            $fail(Lang::get("validation.custom.regon.digits_only"));

            return;
        }

        if (strlen($value) !== 9 && strlen($value) !== 14) {
            $fail(Lang::get("validation.custom.regon.invalid_length"));

            return;
        }

        $this->validate_short_regon($value, $fail);

        if (strlen($value) === 14) {
            $this->validate_long_regon($value, $fail);
        }
    }

    protected function validate_short_regon(string $value, Closure $fail): void
    {
        $weights = [8, 9, 2, 3, 4, 5, 6, 7];
        $digits = str_split($value);
        $checksum = $this->get_checksum($digits, $weights) % 11;

        if ($checksum !== intval($digits[8])) {
            $fail(Lang::get("validation.custom.regon.invalid_checksum"));
        }
    }

    protected function validate_long_regon(string $value, Closure $fail): void
    {
        $weights = [2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8];
        $digits = str_split($value);

        $checksum = $this->get_checksum($digits, $weights) % 11;

        if ($checksum !== intval($digits[13])) {
            $fail(Lang::get("validation.custom.regon.invalid_checksum"));
        }
    }

    /***
     * @param array<string> $number
     * @param array<int> $wages
     */
    protected function get_checksum(array $number, array $wages): int
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

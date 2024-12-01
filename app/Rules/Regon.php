<?php

declare(strict_types=1);

namespace App\Rules;

use App\Helpers\RegonHelper;
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
        $digits = str_split($value);
        $checksum = RegonHelper::calculateChecksum($digits, RegonHelper::WEIGHTS_SHORT) % 11;

        if ($checksum !== intval($digits[8])) {
            $fail(Lang::get("validation.custom.regon.invalid_checksum"));
        }
    }

    protected function validate_long_regon(string $value, Closure $fail): void
    {
        $digits = str_split($value);

        $checksum = RegonHelper::calculateChecksum($digits, RegonHelper::WEIGHTS_LONG) % 11;

        if ($checksum !== intval($digits[13])) {
            $fail(Lang::get("validation.custom.regon.invalid_checksum"));
        }
    }
}

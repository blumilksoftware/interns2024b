<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\School;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Lang;

class IsSchoolValidForRegularUsers implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $school = School::query()->find($value);

        if (!$school) {
            return;
        }

        if ($school->is_disabled | $school->is_admin_school) {
            $fail(Lang::get("validation.custom.school_id.exists"));
        }
    }
}

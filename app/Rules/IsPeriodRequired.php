<?php

namespace App\Rules;

use App\Enums\TimePeriod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsPeriodRequired implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $percentage = request()->input('percentage');
        if (!empty($percentage)) {
            collect(TimePeriod::periodsRequirePercentage())->doesntContain($value)
                ? $fail('The period field is required.')
                : null;
        }
    }
}

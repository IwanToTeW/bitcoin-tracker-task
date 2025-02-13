<?php

namespace App\Rules;

use App\Enums\TimePeriod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsPercentageValid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $period = request()->input('period');

        if (empty($value)) {
            collect(TimePeriod::periodsRequirePercentage())->contains($period)
                ? $fail('The percentage field must be present.')
                : null;
        }

    }
}

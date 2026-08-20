<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Format nombor telefon Malaysia. Terima: 012-3456789, 0123456789,
 * +60123456789, 60123456789, dengan/tanpa dash atau space.
 */
class MalaysianPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/[^0-9+]/', '', (string) $value);

        if (! preg_match('/^(\+?60|0)[0-9]{8,10}$/', $digits)) {
            $fail('Format nombor telefon tidak sah. Contoh: 012-3456789 atau +60123456789.');
        }
    }
}

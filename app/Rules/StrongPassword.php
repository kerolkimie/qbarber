<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Kata laluan: sekurang-kurangnya 8 aksara, 1 huruf besar, 1 simbol.
 */
class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (mb_strlen((string) $value) < 8) {
            $fail('Kata laluan mesti sekurang-kurangnya 8 aksara.');
            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Kata laluan mesti ada sekurang-kurangnya 1 huruf besar (A-Z).');
            return;
        }

        if (! preg_match('/[!@#$%^&*(),.?":{}|<>_\-+=~`\[\]\/\\\\]/', $value)) {
            $fail('Kata laluan mesti ada sekurang-kurangnya 1 simbol (cth: ! @ # $ %).');
        }
    }
}

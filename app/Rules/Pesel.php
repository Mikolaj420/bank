<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Pesel implements ValidationRule
{
    /**
     * Waliduje numer PESEL: 11 cyfr (wyrażenie regularne) oraz poprawną
     * cyfrę kontrolną liczoną z wag 1-3-7-9.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pesel = (string) $value;

        if (! preg_match('/^\d{11}$/', $pesel)) {
            $fail('Pole :attribute musi składać się z 11 cyfr.');

            return;
        }

        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += ((int) $pesel[$i]) * $weights[$i];
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        if ($checkDigit !== (int) $pesel[10]) {
            $fail('Pole :attribute zawiera nieprawidłowy numer PESEL (błędna cyfra kontrolna).');
        }
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RtValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if RT is exactly 3 digits
        if (!preg_match('/^[0-9]{3}$/', $value)) {
            $fail('RT harus terdiri dari 3 digit angka (contoh: 001, 002, 003).');
            return;
        }

        // Check if RT is not 000
        if ($value === '000') {
            $fail('RT tidak boleh 000.');
            return;
        }

        // Check if RT is within valid range (001-999)
        $rtNumber = (int) $value;
        if ($rtNumber < 1 || $rtNumber > 999) {
            $fail('RT harus antara 001-999.');
            return;
        }
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class NikValidation implements ValidationRule
{
    protected $excludeId;
    protected $table;

    public function __construct($excludeId = null, $table = 'penduduks')
    {
        $this->excludeId = $excludeId;
        $this->table = $table;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validasi format NIK (16 digit angka)
        if (!preg_match('/^[0-9]{16}$/', $value)) {
            $fail('NIK harus berupa 16 digit angka.');
            return;
        }

        // Validasi duplikasi
        $query = DB::table($this->table)->where('nik', $value);

        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }

        if ($query->exists()) {
            $fail('NIK sudah terdaftar dalam sistem.');
            return;
        }
    }

}

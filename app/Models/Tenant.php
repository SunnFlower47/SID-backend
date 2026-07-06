<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Daftarkan field custom yang disimpan di dalam kolom JSON 'data'
     * agar dapat diakses sebagai properti Eloquent biasa.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'is_active',
            'status',
            'operator_name',
            'operator_email',
            'operator_password',
            'tenancy_db_name',
        ];
    }

    /**
     * Accessor: decrypt operator_password saat dibaca.
     * Menangani kasus nilai yang belum terenkripsi (plaintext lama).
     */
    public function getOperatorPasswordAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        // Nilai terenkripsi selalu dimulai dengan 'eyJ' (base64 JSON dari Crypt)
        if (str_starts_with($value, 'eyJ')) {
            try {
                return \Illuminate\Support\Facades\Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // fallback jika decrypt gagal
            }
        }

        return $value; // sudah plaintext (belum dimigrasi)
    }

    /**
     * Mutator: encrypt operator_password sebelum disimpan ke database.
     */
    public function setOperatorPasswordAttribute(?string $value): void
    {
        if (!empty($value) && !str_starts_with($value, 'eyJ')) {
            $this->attributes['operator_password'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
        } else {
            $this->attributes['operator_password'] = $value;
        }
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class DataConsistencyValidation implements ValidationRule
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Debug logging removed for production

        // Validasi konsistensi umur vs status perkawinan
        if ($attribute === 'status_perkawinan' && isset($this->data['tanggal_lahir'])) {
            $this->validateAgeVsMaritalStatus($value, $this->data['tanggal_lahir'], $fail);
        }

        // Validasi konsistensi jenis kelamin vs kedudukan keluarga
        if ($attribute === 'kedudukan_keluarga' && isset($this->data['jenis_kelamin'])) {
            $this->validateGenderVsFamilyPosition($value, $this->data['jenis_kelamin'], $fail);
        }

        // Validasi konsistensi tanggal lahir vs status perkawinan
        if ($attribute === 'tanggal_lahir' && isset($this->data['status_perkawinan'])) {
            $this->validateBirthDateVsMaritalStatus($value, $this->data['status_perkawinan'], $fail);
        }

        // Validasi konsistensi kedudukan keluarga vs status perkawinan
        if ($attribute === 'kedudukan_keluarga' && isset($this->data['status_perkawinan'])) {
            $this->validateFamilyPositionVsMaritalStatus($value, $this->data['status_perkawinan'], $fail);
        }
    }

    /**
     * Validasi umur vs status perkawinan
     */
    private function validateAgeVsMaritalStatus($statusPerkawinan, $tanggalLahir, Closure $fail)
    {
        if (!$tanggalLahir) return;

        $usia = Carbon::parse($tanggalLahir)->age;

        // Anak di bawah 10 tahun tidak boleh menikah
        if ($usia < 10 && in_array($statusPerkawinan, ['Kawin', 'Cerai Hidup', 'Cerai Mati'])) {
            $fail("Anak berusia {$usia} tahun tidak boleh memiliki status perkawinan '{$statusPerkawinan}'.");
            return;
        }

        // Anak di bawah 16 tahun tidak boleh menikah (batas minimal pernikahan)
        if ($usia < 16 && in_array($statusPerkawinan, ['Kawin', 'Cerai Hidup', 'Cerai Mati'])) {
            $fail("Anak berusia {$usia} tahun belum memenuhi syarat usia pernikahan (minimal 16 tahun).");
            return;
        }

        // Anak di bawah 12 tahun tidak boleh cerai
        if ($usia < 12 && in_array($statusPerkawinan, ['Cerai Hidup', 'Cerai Mati'])) {
            $fail("Anak berusia {$usia} tahun tidak mungkin sudah cerai.");
            return;
        }
    }

    /**
     * Validasi jenis kelamin vs kedudukan keluarga
     */
    private function validateGenderVsFamilyPosition($kedudukanKeluarga, $jenisKelamin, Closure $fail)
    {
        // Validasi dihapus karena tidak relevan untuk sistem desa
        // Bisa ada kepala keluarga perempuan, istri laki-laki, dll
        // Sistem desa lebih fleksibel untuk struktur keluarga
    }

    /**
     * Validasi tanggal lahir vs status perkawinan
     */
    private function validateBirthDateVsMaritalStatus($tanggalLahir, $statusPerkawinan, Closure $fail)
    {
        if (!$tanggalLahir) return;

        $usia = Carbon::parse($tanggalLahir)->age;

        // Validasi yang sama dengan validateAgeVsMaritalStatus
        $this->validateAgeVsMaritalStatus($statusPerkawinan, $tanggalLahir, $fail);
    }

    /**
     * Validasi kedudukan keluarga vs status perkawinan
     */
    private function validateFamilyPositionVsMaritalStatus($kedudukanKeluarga, $statusPerkawinan, Closure $fail)
    {
        // Validasi dihapus karena terlalu ribet dan case-sensitive
        // Sistem desa lebih fleksibel untuk struktur keluarga
        // Bisa ada istri belum menikah, anak sudah menikah, dll
    }
}

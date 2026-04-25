<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan 'pembaruan_kk' ke ENUM jenis_mutasi di tabel mutasis.
     * Nilai lengkap ENUM harus disebutkan semua saat MODIFY COLUMN di MySQL.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE mutasis MODIFY COLUMN jenis_mutasi ENUM(
                'kelahiran',
                'kematian',
                'pindah_masuk',
                'pindah_keluar',
                'pindah_rt_rw',
                'perubahan_data',
                'pisah_kk',
                'pembaruan_kk'
            ) NOT NULL");
        }
    }

    /**
     * Rollback: hapus 'pembaruan_kk' dari ENUM (kembalikan ke kondisi sebelumnya).
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE mutasis MODIFY COLUMN jenis_mutasi ENUM(
                'kelahiran',
                'kematian',
                'pindah_masuk',
                'pindah_keluar',
                'pindah_rt_rw',
                'perubahan_data',
                'pisah_kk'
            ) NOT NULL");
        }
    }
};

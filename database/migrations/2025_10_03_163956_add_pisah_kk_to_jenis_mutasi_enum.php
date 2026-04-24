<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'pisah_kk' to jenis_mutasi enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE mutasis MODIFY COLUMN jenis_mutasi ENUM('kelahiran', 'kematian', 'pindah_masuk', 'pindah_keluar', 'pindah_rt_rw', 'perubahan_data', 'pisah_kk') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'pisah_kk' from jenis_mutasi enum
        DB::statement("ALTER TABLE mutasis MODIFY COLUMN jenis_mutasi ENUM('kelahiran', 'kematian', 'pindah_masuk', 'pindah_keluar', 'pindah_rt_rw', 'perubahan_data') NOT NULL");
    }
};

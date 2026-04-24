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
        // Update enum values for jenis_mutasi column
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE mutasis MODIFY COLUMN jenis_mutasi ENUM('kelahiran', 'kematian', 'pindah_masuk', 'pindah_keluar', 'pindah_rt_rw', 'perubahan_data') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE mutasis MODIFY COLUMN jenis_mutasi ENUM('kelahiran', 'kematian', 'pindah_masuk', 'pindah_keluar') NOT NULL");
    }
};

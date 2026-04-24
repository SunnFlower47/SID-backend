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
        // First, change the column to string temporarily
        Schema::table('struktur_desas', function (Blueprint $table) {
            $table->string('kategori')->change();
        });

        // Update existing 'staf' data to 'staf_kaur'
        DB::table('struktur_desas')
            ->where('kategori', 'staf')
            ->update(['kategori' => 'staf_kaur']);

        // Then change back to enum with new values
        Schema::table('struktur_desas', function (Blueprint $table) {
            $table->enum('kategori', [
                'kepala_desa',
                'sekretaris',
                'bendahara',
                'kasi_pemerintahan',
                'kasi_kesejahteraan',
                'kasi_pelayanan',
                'kepala_dusun',
                'ketua_rw',
                'ketua_rt',
                'ketua_bumdes',
                'staf_kaur',
                'lainnya'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change to string temporarily
        Schema::table('struktur_desas', function (Blueprint $table) {
            $table->string('kategori')->change();
        });

        // Revert 'staf_kaur' back to 'staf'
        DB::table('struktur_desas')
            ->where('kategori', 'staf_kaur')
            ->update(['kategori' => 'staf']);

        // Revert the enum
        Schema::table('struktur_desas', function (Blueprint $table) {
            $table->enum('kategori', [
                'kepala_desa',
                'sekretaris',
                'bendahara',
                'kasi_pemerintahan',
                'kasi_kesejahteraan',
                'kasi_pelayanan',
                'kepala_dusun',
                'ketua_rw',
                'ketua_rt',
                'ketua_bumdes',
                'staf',
                'lainnya'
            ])->change();
        });
    }
};

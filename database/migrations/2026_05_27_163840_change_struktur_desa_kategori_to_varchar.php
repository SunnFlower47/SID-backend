<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('struktur_desas', function (Blueprint $table) {
            $table->string('kategori', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
                'staf_kasi',
                'lainnya'
            ])->default('lainnya')->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset_inventaris', function (Blueprint $table) {
            // ── Golongan 4: Gedung & Bangunan ─────────────────────────────────
            $table->string('no_imb', 150)->nullable()->after('no_sertifikat')
                  ->comment('Nomor Izin Mendirikan Bangunan');
            $table->decimal('luas_bangunan', 12, 2)->nullable()->after('no_imb')
                  ->comment('Luas bangunan dalam m²');
            $table->unsignedSmallInteger('tahun_dibangun')->nullable()->after('luas_bangunan')
                  ->comment('Tahun pembangunan / perolehan gedung');

            // ── Golongan 5: Jalan, Jaringan & Irigasi ─────────────────────────
            $table->decimal('panjang', 12, 2)->nullable()->after('tahun_dibangun')
                  ->comment('Panjang dalam meter (untuk jalan, saluran, dll)');
            $table->decimal('lebar', 10, 2)->nullable()->after('panjang')
                  ->comment('Lebar dalam meter');
            $table->decimal('volume', 15, 2)->nullable()->after('lebar')
                  ->comment('Volume / luas total (m², m³)');
        });
    }

    public function down(): void
    {
        Schema::table('aset_inventaris', function (Blueprint $table) {
            $table->dropColumn([
                'no_imb', 'luas_bangunan', 'tahun_dibangun',
                'panjang', 'lebar', 'volume',
            ]);
        });
    }
};

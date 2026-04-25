<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom untuk fitur KK Bermasalah (Transisi Kepala Keluarga)
     */
    public function up(): void
    {
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            // Status KK — enum 4 nilai, default normal
            $table->enum('status_kk', ['normal', 'bermasalah', 'bermasalah_sementara', 'resolved'])
                  ->default('normal')
                  ->after('anggota_pisah_kk');

            // ID mutasi yang menjadi penyebab KK bermasalah (disimpan langsung oleh Observer)
            $table->foreignId('mutasi_penyebab_id')
                  ->nullable()
                  ->after('status_kk')
                  ->constrained('mutasis')
                  ->nullOnDelete();

            // ID penduduk yang ditunjuk sebagai KK sementara
            $table->foreignId('kk_sementara_id')
                  ->nullable()
                  ->after('mutasi_penyebab_id')
                  ->constrained('penduduks')
                  ->nullOnDelete();

            // Waktu KK mulai bermasalah (untuk SLA / durasi bermasalah)
            $table->timestamp('kk_bermasalah_sejak')->nullable()->after('kk_sementara_id');

            // Keterangan singkat penyebab bermasalah
            $table->text('catatan_bermasalah')->nullable()->after('kk_bermasalah_sejak');

            // Index untuk query cepat
            $table->index('status_kk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            $table->dropForeign(['mutasi_penyebab_id']);
            $table->dropForeign(['kk_sementara_id']);
            $table->dropIndex(['status_kk']);
            $table->dropColumn([
                'status_kk',
                'mutasi_penyebab_id',
                'kk_sementara_id',
                'kk_bermasalah_sejak',
                'catatan_bermasalah',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom alasan_kurang ke aset_mutasi
        Schema::table('aset_mutasi', function (Blueprint $table) {
            $table->enum('alasan_kurang', [
                'pengadaan_baru',   // jenis tambah: pengadaan baru
                'hibah_masuk',      // jenis tambah: hibah/sumbangan masuk
                'rusak',            // jenis kurang: penghapusan karena rusak
                'dijual',           // jenis kurang: dijual
                'disumbangkan',     // jenis kurang: disumbangkan/hibah keluar
                'dipindahkan',      // jenis kurang: dipindahkan ke instansi lain
                'lainnya',          // fallback
            ])->nullable()->after('keterangan')->comment('Alasan spesifik mutasi (untuk BIM Permendagri)');
        });

        // 2. Update enum asal_usul di aset_inventaris (MySQL ALTER COLUMN)
        //    Tambah nilai: 'Bantuan Provinsi', 'Bantuan Kabupaten'
        DB::statement("ALTER TABLE aset_inventaris MODIFY COLUMN asal_usul ENUM(
            'APBDes',
            'Hibah',
            'Aset Asli Desa',
            'Bantuan Pemerintah',
            'Bantuan Provinsi',
            'Bantuan Kabupaten',
            'Lainnya'
        ) NOT NULL DEFAULT 'APBDes'");
    }

    public function down(): void
    {
        Schema::table('aset_mutasi', function (Blueprint $table) {
            $table->dropColumn('alasan_kurang');
        });

        DB::statement("ALTER TABLE aset_inventaris MODIFY COLUMN asal_usul ENUM(
            'APBDes',
            'Hibah',
            'Aset Asli Desa',
            'Bantuan Pemerintah',
            'Lainnya'
        ) NOT NULL DEFAULT 'APBDes'");
    }
};

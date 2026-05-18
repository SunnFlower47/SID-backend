<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom hierarki Bidang sesuai Permendagri No. 20 Tahun 2018
     * tentang Pengelolaan Keuangan Desa.
     *
     * Struktur APBDes:
     *   Bidang (1-5) → Sub-Bidang → Kegiatan → Rekening
     */
    public function up(): void
    {
        Schema::table('apbdes', function (Blueprint $table) {
            // Bidang 1-5 sesuai Permendagri 20/2018
            $table->tinyInteger('bidang')->nullable()->after('tahun')
                  ->comment('1=Pemerintahan, 2=Pembangunan, 3=Pembinaan, 4=Pemberdayaan, 5=Bencana');

            // Sub-Bidang (contoh: "1.1", "2.3") — input bebas, fleksibel per desa
            $table->string('sub_bidang', 10)->nullable()->after('bidang');

            // Nama Kegiatan di bawah sub-bidang
            $table->string('kegiatan', 200)->nullable()->after('sub_bidang');

            // Perluas sumber_dana: tambah ADD, BHPR, Bantuan Keuangan, Hibah, dll.
            // Ubah kolom sumber_dana menjadi varchar (bukan enum) agar fleksibel
        });

        // Update sumber_dana dari enum ke varchar(50) agar mendukung nilai baru
        // Cek apakah kolom sudah varchar atau masih enum
        $columnType = DB::select("
            SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'apbdes'
              AND COLUMN_NAME = 'sumber_dana'
        ");

        if (!empty($columnType) && strtolower($columnType[0]->DATA_TYPE ?? '') === 'enum') {
            Schema::table('apbdes', function (Blueprint $table) {
                $table->string('sumber_dana', 50)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('apbdes', function (Blueprint $table) {
            $table->dropColumn(['bidang', 'sub_bidang', 'kegiatan']);
        });
    }
};

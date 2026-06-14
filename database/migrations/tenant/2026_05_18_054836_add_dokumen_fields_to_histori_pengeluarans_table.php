<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom dokumen pendukung pengeluaran sesuai Permendagri No. 20/2018
     * Setiap pengeluaran wajib memiliki bukti pembayaran (kwitansi/nota/SPJ)
     */
    public function up(): void
    {
        Schema::table('histori_pengeluarans', function (Blueprint $table) {
            // Nomor bukti otomatis: BKT-{tahun}-{nomor_urut}
            $table->string('no_bukti', 50)->nullable()->after('keterangan');

            // Jenis bukti pembayaran
            $table->enum('jenis_bukti', ['kwitansi', 'nota', 'spj', 'transfer', 'lainnya'])
                  ->default('kwitansi')
                  ->after('no_bukti');

            // Path file bukti (PDF/JPG/PNG)
            $table->string('file_bukti', 255)->nullable()->after('jenis_bukti');

            // Nama file asli untuk tampilan
            $table->string('nama_file_bukti', 255)->nullable()->after('file_bukti');

            // Status SPJ (Surat Pertanggungjawaban)
            $table->enum('spj_status', ['belum', 'sudah'])->default('belum')->after('nama_file_bukti');
        });
    }

    public function down(): void
    {
        Schema::table('histori_pengeluarans', function (Blueprint $table) {
            $table->dropColumn([
                'no_bukti',
                'jenis_bukti',
                'file_bukti',
                'nama_file_bukti',
                'spj_status',
            ]);
        });
    }
};

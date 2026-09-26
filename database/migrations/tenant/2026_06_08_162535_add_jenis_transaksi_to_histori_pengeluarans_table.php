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
        Schema::table('histori_pengeluarans', function (Blueprint $table) {
            // pencairan_panjar = Kas Desa memberikan uang muka ke Pelaksana Kegiatan (Penerimaan PK)
            // belanja = PK membelanjakan uang untuk kegiatan (Pengeluaran PK)
            // kembali_sisa = PK mengembalikan sisa uang ke Kas Desa (Pengeluaran PK / Penerimaan Desa)
            $table->enum('jenis_transaksi', ['pencairan_panjar', 'belanja', 'kembali_sisa'])->default('belanja')->after('apbdes_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histori_pengeluarans', function (Blueprint $table) {
            $table->dropColumn('jenis_transaksi');
        });
    }
};

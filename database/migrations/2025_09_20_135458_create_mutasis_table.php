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
        Schema::create('mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_mutasi', ['kelahiran', 'kematian', 'pindah_masuk', 'pindah_keluar', 'pindah_rt_rw', 'perubahan_data', 'pisah_kk']);
            $table->string('kategori_mutasi', 100);
            $table->string('asal_tujuan');
            $table->date('tanggal_mutasi');
            $table->text('alasan');
            $table->string('dokumen_pendukung')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasis');
    }
};

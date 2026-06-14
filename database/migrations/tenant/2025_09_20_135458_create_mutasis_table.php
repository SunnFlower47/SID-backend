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
            $table->foreignId('penduduk_id')->constrained('penduduks')->onDelete('cascade');
            
            $table->enum('jenis_mutasi', [
                'kelahiran',
                'kematian',
                'pindah_masuk',
                'pindah_keluar',
                'pindah_rt_rw',
                'pisah_kk',
                'pembaruan_kk'
            ]);
            
            $table->date('tanggal_mutasi');
            $table->text('alasan')->nullable();
            $table->json('detail_tambahan')->nullable(); // Menampung metadata spesifik (ex: No Akta)
            $table->string('dokumen_pendukung')->nullable(); // Ditambahkan sesuai db lama
            
            // Kolom pembantu untuk laporan
            $table->string('kategori_mutasi')->nullable(); // ex: Antar Desa, Antar Kecamatan
            $table->string('asal_tujuan')->nullable();
            
            $table->softDeletes();
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

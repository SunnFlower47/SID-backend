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
        Schema::create('kartu_keluargas', function (Blueprint $table) {
            $table->id();
            
            // Wilayah IDs (Source of Truth)
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
            
            $table->string('nkk', 16)->unique();
            $table->json('history_nkk')->nullable(); // Dikembalikan ke JSON sesuai db lama
            $table->string('nama_kepala_keluarga')->nullable();
            $table->string('nik_kepala_keluarga', 16)->nullable();
            $table->text('alamat')->nullable();
            
            // Statistics (Auto-updated by Recalculate Logic)
            $table->integer('jumlah_anggota')->default(0);
            $table->integer('anggota_aktif')->default(0); 
            $table->integer('anggota_mutasi')->default(0); 
            $table->integer('anggota_meninggal')->default(0);
            $table->integer('anggota_pindah')->default(0);
            $table->integer('anggota_pisah_kk')->default(0);
            
            // KK Bermasalah Logic (Phase 5)
            $table->enum('status_kk', ['normal', 'bermasalah', 'bermasalah_sementara', 'resolved'])->default('normal');
            $table->foreignId('mutasi_penyebab_id')->nullable(); // ID mutasi yang menyebabkan KK bermasalah (ex: Kematian Kepala Keluarga)
            $table->unsignedBigInteger('kk_sementara_id')->nullable(); // ID penduduk yang ditunjuk jadi Kepala Keluarga sementara
            $table->timestamp('kk_bermasalah_sejak')->nullable();
            $table->text('catatan_bermasalah')->nullable(); // Dikembalikan ke TEXT sesuai db lama

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('nama_kepala_keluarga');
            $table->index(['rt_id', 'rw_id', 'dusun_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_keluargas');
    }
};

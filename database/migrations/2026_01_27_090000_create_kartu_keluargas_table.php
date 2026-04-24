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
            $table->string('nkk', 16)->unique();
            $table->string('nama_kepala_keluarga')->nullable();
            $table->string('nik_kepala_keluarga', 16)->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('dusun')->nullable();
            $table->integer('jumlah_anggota')->default(0);
            $table->integer('anggota_aktif')->default(0); // non-mutasi, non-meninggal
            $table->integer('anggota_mutasi')->default(0); // total mutasi
            $table->integer('anggota_meninggal')->default(0);
            $table->integer('anggota_pindah')->default(0);
            $table->integer('anggota_pisah_kk')->default(0);
            $table->timestamps();

            // Indexes for faster search/sort
            $table->index('nama_kepala_keluarga');
            $table->index('dusun');
            $table->index(['rt', 'rw', 'dusun']);
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

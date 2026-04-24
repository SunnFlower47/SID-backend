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
        Schema::create('histori_pisah_kk', function (Blueprint $table) {
            $table->id();
            $table->string('nik_penduduk', 16);
            $table->string('nama_penduduk');
            $table->string('nkk_lama', 16); // NKK sebelum pisah
            $table->string('nkk_baru', 16); // NKK setelah pisah
            $table->string('alamat_lama');
            $table->string('alamat_baru');
            $table->string('rt_lama', 3);
            $table->string('rw_lama', 3);
            $table->string('rt_baru', 3);
            $table->string('rw_baru', 3);
            $table->string('dusun_lama')->nullable();
            $table->string('dusun_baru')->nullable();
            $table->boolean('pindah_alamat')->default(false); // Apakah ikut pindah alamat
            $table->string('alasan_pisah')->nullable(); // Nikah, dewasa, dll
            $table->text('keterangan')->nullable();
            $table->string('status')->default('selesai'); // selesai, batal
            $table->unsignedBigInteger('user_id'); // Petugas yang memproses
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['nik_penduduk', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_pisah_kk');
    }
};

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
        Schema::create('bantuan_sosials', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program');
            $table->string('jenis_bantuan'); // BLT, PKH, BPNT, Bansos Lainnya
            $table->text('deskripsi');
            $table->decimal('nilai_bantuan', 15, 2)->nullable();
            $table->string('periode'); // 2024, 2024-2025, dll
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['aktif', 'selesai', 'ditangguhkan']);
            $table->json('kriteria_penerima'); // Kriteria penerima bantuan
            $table->string('sumber_dana'); // APBN, APBD, Swasta, dll
            $table->integer('kuota_penerima')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_sosials');
    }
};

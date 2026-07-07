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
        Schema::create('buku_ekspedisis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pengiriman');
            $table->date('tanggal_surat');
            $table->string('nomor_surat');
            $table->text('isi_singkat');
            $table->string('tujuan');
            $table->string('penerima')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_ekspedisis');
    }
};

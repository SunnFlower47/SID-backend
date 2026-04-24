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
        Schema::create('penerima_bantuan_sosials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bantuan_sosial_id')->constrained()->onDelete('cascade');
            $table->foreignId('penduduk_id')->constrained()->onDelete('cascade');
            $table->string('nomor_kartu')->nullable(); // Nomor kartu bantuan
            $table->decimal('nilai_diterima', 15, 2);
            $table->date('tanggal_penerimaan');
            $table->enum('status_penerimaan', ['aktif', 'ditangguhkan', 'dihentikan']);
            $table->text('keterangan')->nullable();
            $table->json('data_tambahan')->nullable(); // Data tambahan penerima
            $table->timestamps();

            $table->unique(['bantuan_sosial_id', 'penduduk_id'], 'unique_penerima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerima_bantuan_sosials');
    }
};

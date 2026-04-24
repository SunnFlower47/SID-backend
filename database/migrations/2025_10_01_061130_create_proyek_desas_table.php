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
        Schema::create('proyek_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek');
            $table->text('deskripsi');
            $table->enum('jenis', ['infrastruktur', 'sosial', 'ekonomi', 'lingkungan', 'lainnya']);
            $table->decimal('anggaran', 15, 2);
            $table->decimal('realisasi', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['perencanaan', 'pelaksanaan', 'selesai', 'tertunda', 'dibatalkan'])->default('perencanaan');
            $table->integer('progress')->default(0); // 0-100
            $table->string('lokasi');
            $table->string('penanggung_jawab');
            $table->string('kontraktor')->nullable();
            $table->text('dokumentasi')->nullable(); // JSON array of file paths
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_desas');
    }
};

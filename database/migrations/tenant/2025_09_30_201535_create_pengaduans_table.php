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
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('nik_pelapor')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('alamat');
            $table->string('kategori'); // infrastruktur, keamanan, kebersihan, dll
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('lokasi')->nullable();
            $table->json('foto')->nullable(); // Array of photo paths
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'darurat']);
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditolak']);
            $table->text('tanggapan')->nullable();
            $table->timestamp('tanggal_tanggapan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Admin yang menangani
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};

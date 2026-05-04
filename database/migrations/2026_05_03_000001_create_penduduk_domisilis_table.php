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
        Schema::create('penduduk_domisilis', function (Blueprint $table) {
            $table->id();

            // === DATA KTP / ASAL ===
            $table->string('nik', 16)->index();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20);
            $table->string('agama')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('asal_daerah')->nullable(); // Kota/Kabupaten asal
            $table->text('alamat_asal')->nullable();    // Alamat sesuai KTP

            // === DATA DOMISILI DI DESA ===
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
            $table->text('alamat_tinggal')->nullable();
            $table->string('keperluan_domisili')->nullable(); // kerja, sekolah, ikut_keluarga, lainnya

            // === MASA BERLAKU ===
            $table->date('tanggal_masuk');
            $table->date('tanggal_berlaku'); // +3 bulan dari masuk/perpanjang
            $table->enum('status', ['aktif', 'expired', 'dicabut'])->default('aktif');
            $table->unsignedInteger('perpanjangan_ke')->default(0); // Berapa kali sudah diperpanjang

            // === INTEGRASI SURAT ===
            $table->string('nomor_surat')->nullable(); // Nomor surat domisili terakhir

            // === TRACKING & CATATAN ===
            $table->text('catatan')->nullable(); // Alasan cabut / info tambahan
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            // Indexes untuk performa filter & pencarian
            $table->index('status');
            $table->index('tanggal_berlaku');
            $table->index(['rt_id', 'rw_id', 'dusun_id']);
            $table->index('asal_daerah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduk_domisilis');
    }
};

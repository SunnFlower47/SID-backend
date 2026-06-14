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
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            // Apakah surat sudah ditandatangani secara elektronik (TTE)
            $table->boolean('is_tte')->default(false)->after('status');
            // Waktu TTE dilakukan
            $table->timestamp('tte_at')->nullable()->after('is_tte');
            // Path file PDF yang sudah ditandatangani (disimpan di storage)
            $table->string('signed_pdf_path')->nullable()->after('tte_at');
            // NIK pejabat yang menandatangani
            $table->string('tte_signer_nik', 20)->nullable()->after('signed_pdf_path');
            // Nama pejabat yang menandatangani (snapshot agar tidak berubah)
            $table->string('tte_signer_name')->nullable()->after('tte_signer_nik');
            // Token unik untuk URL verifikasi QR Code (sudah ada di kolom qr_token?)
            // Pastikan kolom qr_token sudah ada, jika belum buat di sini
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['is_tte', 'tte_at', 'signed_pdf_path', 'tte_signer_nik', 'tte_signer_name']);
        });
    }
};

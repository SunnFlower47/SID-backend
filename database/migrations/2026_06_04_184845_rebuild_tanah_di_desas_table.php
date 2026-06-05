<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First drop the mutasi table to avoid FK constraint errors, then drop the main table.
        Schema::dropIfExists('tanah_di_desa_mutasis');
        Schema::dropIfExists('tanah_di_desas');

        // Recreate tanah_di_desas exactly matching 21 columns A.6 (Permendagri 47/2016)
        Schema::create('tanah_di_desas', function (Blueprint $table) {
            $table->id(); // Kolom 1 (Nomor Urut)
            
            // Extra Field for Integration
            $table->string('nop')->nullable(); // NOP Pajak PBB Integration
            
            // 1. Data Identitas dan Pemilik
            $table->string('nama_pemilik'); // Kolom 2
            $table->string('tempat_lahir_berdiri')->nullable(); // Kolom 3
            $table->date('tanggal_lahir_berdiri')->nullable(); // Kolom 4
            
            // 2. Status Legalitas dan Riwayat
            $table->string('status_kepemilikan'); // Kolom 5
            $table->date('tanggal_perolehan')->nullable(); // Kolom 6
            $table->string('no_sertifikat')->nullable(); // Kolom 7
            $table->date('tanggal_penerbitan_sertifikat')->nullable(); // Kolom 8
            $table->string('no_buku_c')->nullable(); // Kolom 9
            $table->string('no_persil')->nullable(); // Kolom 10
            $table->string('no_kelas')->nullable(); // Kolom 11
            
            // 3. Klasifikasi Luas Tanah Pertanian
            $table->decimal('luas_sawah', 15, 2)->default(0); // Kolom 12
            $table->decimal('luas_tegalan', 15, 2)->default(0); // Kolom 13
            $table->decimal('luas_kebun', 15, 2)->default(0); // Kolom 14
            
            // 4. Klasifikasi Luas Tanah Non-Pertanian
            $table->decimal('luas_perumahan', 15, 2)->default(0); // Kolom 15
            $table->decimal('luas_industri', 15, 2)->default(0); // Kolom 16
            $table->decimal('luas_fasilitas_umum', 15, 2)->default(0); // Kolom 17
            $table->decimal('luas_lain_lain', 15, 2)->default(0); // Kolom 18
            
            // 5. Data Lokasi dan Mutasi
            $table->text('lokasi_tanah')->nullable(); // Kolom 19 (RT/RW/Dusun)
            $table->string('batas_utara')->nullable(); // Kolom 20
            $table->string('batas_timur')->nullable();
            $table->string('batas_selatan')->nullable();
            $table->string('batas_barat')->nullable();
            
            $table->text('keterangan')->nullable(); // Kolom 21
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Recreate mutasi table if it's supposed to be linked with FK
        Schema::create('tanah_di_desa_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanah_di_desa_id')->constrained('tanah_di_desas')->cascadeOnDelete();
            $table->string('pemilik_lama');
            $table->string('pemilik_baru');
            $table->date('tanggal_mutasi');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanah_di_desa_mutasis');
        Schema::dropIfExists('tanah_di_desas');
    }
};

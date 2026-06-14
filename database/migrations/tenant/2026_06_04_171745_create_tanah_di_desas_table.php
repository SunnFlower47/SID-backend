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
        Schema::create('tanah_di_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemilik'); // Nama Perorangan / Badan Hukum
            $table->decimal('luas_tanah', 15, 2)->default(0); // Jumlah Luas (M2)

            // Status Hak Tanah (M2)
            $table->decimal('status_hm', 15, 2)->default(0); // Hak Milik
            $table->decimal('status_hgb', 15, 2)->default(0); // Hak Guna Bangunan
            $table->decimal('status_hp', 15, 2)->default(0); // Hak Pakai
            $table->decimal('status_hgu', 15, 2)->default(0); // Hak Guna Usaha
            $table->decimal('status_hpl', 15, 2)->default(0); // Hak Pengelolaan
            $table->decimal('status_ma', 15, 2)->default(0); // Milik Adat
            $table->decimal('status_tn', 15, 2)->default(0); // Tanah Negara
            $table->decimal('status_td', 15, 2)->default(0); // Tanah Desa
            $table->decimal('belum_bersertifikat', 15, 2)->default(0); // Belum Bersertifikat

            // Penggunaan Tanah (M2)
            $table->decimal('penggunaan_perumahan', 15, 2)->default(0);
            $table->decimal('penggunaan_perdagangan', 15, 2)->default(0);
            $table->decimal('penggunaan_perkantoran', 15, 2)->default(0);
            $table->decimal('penggunaan_industri', 15, 2)->default(0);
            $table->decimal('penggunaan_fasilitas_umum', 15, 2)->default(0);
            $table->decimal('penggunaan_sawah', 15, 2)->default(0);
            $table->decimal('penggunaan_tegalan', 15, 2)->default(0);
            $table->decimal('penggunaan_perkebunan', 15, 2)->default(0);
            $table->decimal('penggunaan_peternakan', 15, 2)->default(0);
            $table->decimal('penggunaan_hutan', 15, 2)->default(0);
            $table->decimal('penggunaan_kosong', 15, 2)->default(0);
            $table->decimal('penggunaan_lain', 15, 2)->default(0);

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
        Schema::dropIfExists('tanah_di_desas');
    }
};

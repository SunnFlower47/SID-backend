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
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('nik_pemilik')->nullable();
            $table->string('alamat_usaha');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('dusun')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->enum('jenis_usaha', ['makanan', 'minuman', 'kerajinan', 'jasa', 'perdagangan', 'pertanian', 'peternakan', 'lainnya']);
            $table->text('deskripsi_usaha')->nullable();
            $table->decimal('modal_awal', 15, 2)->nullable();
            $table->decimal('omset_bulanan', 15, 2)->nullable();
            $table->integer('jumlah_karyawan')->default(0);
            $table->string('status_usaha')->default('aktif'); // aktif, tutup, pindah
            $table->date('tanggal_berdiri')->nullable();
            $table->json('produk_unggulan')->nullable(); // array of products
            $table->json('foto_usaha')->nullable(); // array of photos
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_unggulan')->default(false); // produk unggulan desa
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};

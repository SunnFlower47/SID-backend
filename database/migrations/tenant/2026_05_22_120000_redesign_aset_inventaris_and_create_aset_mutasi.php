<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop tabel lama (ada data test, tidak apa-apa)
        Schema::dropIfExists('aset_inventaris');

        // Buat ulang aset_inventaris — sekarang menyimpan data ASET PERMANEN
        // bukan per periode/tahun
        Schema::create('aset_inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_barang_id')->constrained('aset_barangs')->cascadeOnDelete();

            // Identitas barang
            $table->string('nama_barang_override')->nullable(); // nama kustom (opsional)
            $table->string('satuan');
            $table->string('lokasi')->nullable();               // lokasi fisik aset
            $table->date('tanggal_perolehan')->nullable();      // kapan pertama dimiliki desa
            $table->enum('asal_usul', [
                'APBDes',
                'Hibah',
                'Aset Asli Desa',
                'Bantuan Pemerintah',
                'Lainnya',
            ])->default('APBDes');

            // Kondisi fisik saat ini
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');

            // Keterangan tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });

        // Tabel mutasi: log semua perubahan (tambah/kurang) per transaksi
        Schema::create('aset_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_inventaris_id')
                  ->constrained('aset_inventaris')
                  ->cascadeOnDelete();

            $table->integer('tahun');
            $table->tinyInteger('semester')->default(1); // 1 atau 2
            $table->date('tanggal');                     // tanggal transaksi
            $table->enum('jenis', ['tambah', 'kurang']);

            $table->decimal('kwantitas', 15, 2)->default(0);
            $table->decimal('nilai', 20, 2)->default(0);

            $table->string('keterangan')->nullable(); // alasan/sumber (pengadaan, hibah, rusak, dll)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_mutasi');
        Schema::dropIfExists('aset_inventaris');
    }
};

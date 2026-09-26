<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_barang_id')->constrained('aset_barangs')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun')->comment('Tahun anggaran');

            // Override nama & satuan jika berbeda dari master (e.g. "Kendaraan Roda 2 YAMAHA NMAX")
            $table->string('nama_barang_override', 200)->nullable();
            $table->string('satuan', 30)->nullable();

            // Saldo Awal (per 1 Januari tahun berjalan)
            $table->decimal('saldo_awal_kwantitas', 15, 2)->default(0);
            $table->decimal('saldo_awal_nilai', 20, 2)->default(0);

            // Mutasi Bertambah (pengadaan, hibah, dll)
            $table->decimal('mutasi_tambah_kwantitas', 15, 2)->default(0);
            $table->decimal('mutasi_tambah_nilai', 20, 2)->default(0);
            $table->string('keterangan_tambah', 255)->nullable();

            // Mutasi Berkurang (rusak, jual, hapus, pindah)
            $table->decimal('mutasi_kurang_kwantitas', 15, 2)->default(0);
            $table->decimal('mutasi_kurang_nilai', 20, 2)->default(0);
            $table->string('keterangan_kurang', 255)->nullable();

            // Saldo akhir dihitung via accessor: awal + tambah - kurang
            // (tidak disimpan di DB agar selalu akurat)

            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->string('lokasi', 200)->nullable()->comment('Lokasi penyimpanan/penggunaan aset');
            $table->text('keterangan')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Satu barang hanya boleh punya satu record per tahun
            $table->unique(['aset_barang_id', 'tahun', 'nama_barang_override'], 'uq_aset_per_tahun');
            $table->index(['tahun', 'aset_barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_inventaris');
    }
};

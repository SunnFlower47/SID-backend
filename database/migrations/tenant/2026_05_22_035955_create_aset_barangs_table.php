<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_kategori_id')->constrained('aset_kategoris')->cascadeOnDelete();
            $table->string('kode_barang', 20)->unique()->comment('Format: X.XX.XX.XX');
            $table->string('nama_barang', 200);
            $table->string('satuan_default', 30)->nullable()->comment('m², unit, Buah, Lusin, dll');
            $table->timestamps();
            $table->softDeletes();

            $table->index('kode_barang');
            $table->index('aset_kategori_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_barangs');
    }
};

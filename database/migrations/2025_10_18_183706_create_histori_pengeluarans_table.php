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
        Schema::create('histori_pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengeluaran'); // Nama pengeluaran
            $table->foreignId('apbdes_id')->constrained()->onDelete('cascade'); // Dari rekening mana
            $table->decimal('jumlah', 15, 2); // Jumlah pengeluaran
            $table->date('tanggal_pengeluaran'); // Tanggal pengeluaran
            $table->text('keterangan')->nullable(); // Keterangan tambahan
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User yang membuat pengeluaran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_pengeluarans');
    }
};

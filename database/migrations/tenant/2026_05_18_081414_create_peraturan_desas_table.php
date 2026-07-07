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
        Schema::create('peraturan_desas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_peraturan', ['APBDes', 'Perubahan APBDes', 'Lpj APBDes', 'Lainnya'])->default('APBDes');
            $table->year('tahun_anggaran');
            $table->string('judul');
            $table->string('nomor_peraturan')->nullable();
            $table->date('tanggal_ditetapkan')->nullable();
            $table->enum('status', ['draft', 'diajukan_bpd', 'dibahas', 'disetujui', 'ditolak'])->default('draft');
            $table->text('keterangan_bpd')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tahun_anggaran', 'jenis_peraturan', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peraturan_desas');
    }
};

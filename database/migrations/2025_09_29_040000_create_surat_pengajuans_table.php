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
        Schema::create('surat_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('surat_type');
            $table->foreignId('penduduk_id')->constrained()->onDelete('cascade');
            $table->string('nomor_surat')->unique();
            $table->text('keperluan')->nullable();
            $table->string('tujuan')->nullable();
            $table->date('tanggal_surat');
            $table->text('keterangan_tambahan')->nullable();
            $table->json('data_tambahan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pengajuans');
    }
};


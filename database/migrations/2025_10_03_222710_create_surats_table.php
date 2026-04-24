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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat');
            $table->unsignedBigInteger('penduduk_id');
            $table->string('nomor_surat');
            $table->text('keperluan')->nullable();
            $table->string('tujuan')->nullable();
            $table->date('tanggal_surat');
            $table->year('tahun')->default(2025);
            $table->text('keterangan_tambahan')->nullable();
            $table->json('data_tambahan')->nullable();
            $table->enum('status', ['selesai', 'arsip'])->default('selesai');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Add foreign key constraints separately
            $table->foreign('penduduk_id')->references('id')->on('penduduks')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Add unique constraint separately
            $table->unique('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};

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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            
            // Relational ID (Source of Truth for Wilayah and NKK)
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluargas')->nullOnDelete();
            
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('jenis_kelamin', 20);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('kedudukan_keluarga')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            
            $table->text('keterangan')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            // Indexes for Performance
            $table->index('nama');
            $table->index('kartu_keluarga_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};

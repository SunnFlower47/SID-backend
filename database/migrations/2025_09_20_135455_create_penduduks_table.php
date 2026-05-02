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
            $table->string('nkk'); // No. KK
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('jenis_kelamin', 20);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('agama');
            $table->string('status_perkawinan')->nullable();
            $table->string('kedudukan_keluarga')->nullable();
            $table->string('pendidikan');
            $table->string('pekerjaan');
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->text('alamat');
            $table->string('rt');
            $table->string('rw');
            $table->string('dusun')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 20)->default('Aktif');
            $table->softDeletes();
            $table->timestamps();
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

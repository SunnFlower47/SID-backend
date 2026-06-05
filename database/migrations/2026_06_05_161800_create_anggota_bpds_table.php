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
        Schema::create('anggota_bpds', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('nama')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('jabatan')->nullable(); // Ketua, Wakil Ketua, Sekretaris, Anggota
            $table->string('no_keputusan_pengangkatan')->nullable();
            $table->date('tanggal_keputusan_pengangkatan')->nullable();
            $table->string('no_keputusan_pemberhentian')->nullable();
            $table->date('tanggal_keputusan_pemberhentian')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_bpds');
    }
};

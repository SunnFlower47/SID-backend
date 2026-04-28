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
        Schema::table('penduduks', function (Blueprint $table) {
            // Kita buat nullable agar kodingan baru yang tidak mengirim kartu_keluarga_id tidak error
            // Ini solusi aman karena Anda belum yakin untuk menghapus kolomnya secara fisik.
            $table->bigInteger('kartu_keluarga_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->bigInteger('kartu_keluarga_id')->unsigned()->nullable(false)->change();
        });
    }
};

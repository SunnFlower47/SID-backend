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
        Schema::create('keputusan_kades', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_keputusan')->unique();
            $table->string('judul_keputusan');
            $table->date('tanggal_ditetapkan');
            $table->text('keterangan')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keputusan_kades');
    }
};

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
        Schema::create('mutasi_banks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_mutasi');
            $table->enum('jenis_mutasi', ['setor', 'tarik', 'bunga', 'admin_bank', 'lainnya']);
            $table->string('uraian');
            $table->decimal('jumlah', 15, 2);
            $table->string('no_bukti')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_banks');
    }
};

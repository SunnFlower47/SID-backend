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
        Schema::create('pajak_pbb_tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pajak_pbb_objek_id')->constrained()->onDelete('cascade');
            $table->string('tahun');
            $table->integer('pbb_terhutang');
            $table->string('jatuh_tempo')->nullable();
            $table->string('status');
            $table->string('tanggal_bayar')->nullable();
            $table->integer('denda')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajak_pbb_tagihans');
    }
};

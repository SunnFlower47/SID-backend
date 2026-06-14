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
        Schema::create('penerimaan_kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_penerimaan');
            $table->string('uraian');
            $table->unsignedBigInteger('apbdes_id')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->string('no_bukti')->nullable();
            $table->string('penyetor')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->foreign('apbdes_id')->references('id')->on('apbdes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_kas');
    }
};

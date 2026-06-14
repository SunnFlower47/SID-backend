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
        Schema::create('pajak_pbb_objeks', function (Blueprint $table) {
            $table->id();
            $table->string('nop')->unique();
            $table->string('nama_wp')->nullable();
            $table->text('alamat_wp')->nullable();
            $table->text('alamat_objek')->nullable();
            $table->integer('luas_bumi')->nullable();
            $table->integer('luas_bangunan')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajak_pbb_objeks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()->comment('Kode golongan: 2, 3, 4, 5, 6');
            $table->string('nama', 100)->comment('Nama golongan aset');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_kategoris');
    }
};

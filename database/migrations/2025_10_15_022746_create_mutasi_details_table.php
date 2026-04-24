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
        Schema::create('mutasi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mutasi_id')->constrained()->onDelete('cascade');
            $table->string('field_name'); // nama field seperti 'hari_meninggal', 'nama_bayi', dll
            $table->text('field_value'); // nilai field
            $table->string('field_type')->default('string'); // string, date, time, number
            $table->timestamps();

            $table->index(['mutasi_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_details');
    }
};

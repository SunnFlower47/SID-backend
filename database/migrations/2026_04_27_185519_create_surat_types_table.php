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
        Schema::create('surat_types', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g., 'sku', 'ahli-waris'
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->text('persyaratan')->nullable();
            $table->boolean('has_template')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('form_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_types');
    }
};

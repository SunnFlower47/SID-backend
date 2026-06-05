<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_type_templates', function (Blueprint $table) {
            $table->id();
            $table->string('surat_type_id');
            $table->foreign('surat_type_id')
                  ->references('id')
                  ->on('surat_types')
                  ->cascadeOnDelete();
            $table->string('kode', 20);           // N1, N2, WALI, UMUM, dll
            $table->string('nama', 150);           // Nama lengkap dokumen
            $table->text('deskripsi')->nullable();
            $table->string('file_template')->nullable(); // nama file .docx
            $table->json('form_json')->nullable();  // form khusus per sub-template
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->enum('gender_filter', ['all', 'L', 'P'])->default('all');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_type_templates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wilayah_import_conflicts', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 80)->index();
            $table->string('source_file')->nullable();
            $table->string('sheet_name')->nullable();
            $table->unsignedInteger('row_number')->nullable();
            $table->string('nik', 32)->nullable()->index();
            $table->string('nama')->nullable();
            $table->string('nkk', 32)->nullable()->index();
            $table->string('rw_raw', 16)->nullable();
            $table->string('rt_raw', 16)->nullable();
            $table->string('dusun_raw')->nullable();
            $table->string('reason')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_import_conflicts');
    }
};

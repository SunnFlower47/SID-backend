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
        Schema::create('dusuns', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->nullable()->unique();
            $table->string('nama', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_generated')->default(false);
            $table->boolean('needs_review')->default(false);
            $table->timestamps();
        });

        Schema::create('rws', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 3)->unique(); // 001, 002, dst
            $table->string('nama', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_generated')->default(false);
            $table->boolean('needs_review')->default(false);
            $table->timestamps();
        });

        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 3); // 001, 002, dst
            $table->string('nama', 100)->nullable();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_generated')->default(false);
            $table->boolean('needs_review')->default(false);
            $table->timestamps();

            $table->unique(['kode', 'rw_id']);
            $table->index(['dusun_id', 'rw_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rts');
        Schema::dropIfExists('rws');
        Schema::dropIfExists('dusuns');
    }
};

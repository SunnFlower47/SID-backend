<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wilayah_change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 30);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('action', 50)->default('update');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('preview_token', 120)->nullable()->index();
            $table->json('before_payload')->nullable();
            $table->json('after_payload')->nullable();
            $table->json('backup_payload')->nullable();
            $table->unsignedInteger('affected_count')->default(0);
            $table->string('status', 20)->default('applied');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            $table->unsignedBigInteger('rolled_back_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_change_logs');
    }
};

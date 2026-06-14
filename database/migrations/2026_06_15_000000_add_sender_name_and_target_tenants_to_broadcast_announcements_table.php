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
        Schema::table('broadcast_announcements', function (Blueprint $table) {
            $table->string('sender_name')->default('Diskominfo')->after('type');
            $table->string('target_type')->default('all')->after('sender_name'); // 'all' or 'specific'
            $table->json('target_tenant_ids')->nullable()->after('target_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcast_announcements', function (Blueprint $table) {
            $table->dropColumn(['sender_name', 'target_type', 'target_tenant_ids']);
        });
    }
};

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
        Schema::table('surat_types', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_types', 'template_code')) {
                $table->string('template_code')->nullable()->after('has_template');
            }
            if (!Schema::hasColumn('surat_types', 'icon')) {
                $table->string('icon')->nullable()->after('template_code');
            }
            if (!Schema::hasColumn('surat_types', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->dropColumn(['template_code', 'icon', 'color']);
        });
    }
};

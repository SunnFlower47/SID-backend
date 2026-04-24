<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wilayah_import_conflicts', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayah_import_conflicts', 'issue_type')) {
                $table->string('issue_type', 50)->default('wilayah_conflict')->after('reason')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_import_conflicts', function (Blueprint $table) {
            if (Schema::hasColumn('wilayah_import_conflicts', 'issue_type')) {
                $table->dropColumn('issue_type');
            }
        });
    }
};

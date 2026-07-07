<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wilayah_import_conflicts', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayah_import_conflicts', 'payload_raw')) {
                $table->json('payload_raw')->nullable()->after('meta');
            }
            if (!Schema::hasColumn('wilayah_import_conflicts', 'payload_fixed')) {
                $table->json('payload_fixed')->nullable()->after('payload_raw');
            }
            if (!Schema::hasColumn('wilayah_import_conflicts', 'resolution_action')) {
                $table->string('resolution_action', 80)->nullable()->after('payload_fixed');
            }
            if (!Schema::hasColumn('wilayah_import_conflicts', 'reprocessed_at')) {
                $table->timestamp('reprocessed_at')->nullable()->after('resolved_at');
            }
            if (!Schema::hasColumn('wilayah_import_conflicts', 'reprocess_status')) {
                $table->string('reprocess_status', 20)->nullable()->after('reprocessed_at')->index();
            }
            if (!Schema::hasColumn('wilayah_import_conflicts', 'reprocess_message')) {
                $table->text('reprocess_message')->nullable()->after('reprocess_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_import_conflicts', function (Blueprint $table) {
            $drops = [];
            foreach (['payload_raw','payload_fixed','resolution_action','reprocessed_at','reprocess_status','reprocess_message'] as $col) {
                if (Schema::hasColumn('wilayah_import_conflicts', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};

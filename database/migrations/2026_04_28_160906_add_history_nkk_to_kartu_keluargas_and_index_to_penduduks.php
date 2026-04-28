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
        // 1. Add history_nkk to kartu_keluargas
        if (!Schema::hasColumn('kartu_keluargas', 'history_nkk')) {
            Schema::table('kartu_keluargas', function (Blueprint $table) {
                $table->json('history_nkk')->nullable()->after('nkk');
            });
        }

        // 2. Add Index to kartu_keluarga_id in penduduks for performance
        Schema::table('penduduks', function (Blueprint $table) {
            $indexes = Schema::getIndexes('penduduks');
            $indexNames = array_column($indexes, 'name');
            
            if (!in_array('penduduks_kartu_keluarga_id_index', $indexNames)) {
                $table->index('kartu_keluarga_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('kartu_keluargas', 'history_nkk')) {
            Schema::table('kartu_keluargas', function (Blueprint $table) {
                $table->dropColumn('history_nkk');
            });
        }

        Schema::table('penduduks', function (Blueprint $table) {
            $indexes = Schema::getIndexes('penduduks');
            $indexNames = array_column($indexes, 'name');
            
            if (in_array('penduduks_kartu_keluarga_id_index', $indexNames)) {
                $table->dropIndex(['kartu_keluarga_id']);
            }
        });
    }
};

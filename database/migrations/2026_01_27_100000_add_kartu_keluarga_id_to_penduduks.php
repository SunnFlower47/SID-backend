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
        if (!Schema::hasColumn('penduduks', 'kartu_keluarga_id')) {
            Schema::table('penduduks', function (Blueprint $table) {
                $table->foreignId('kartu_keluarga_id')->nullable()->after('id')->constrained('kartu_keluargas')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('penduduks', 'kartu_keluarga_id')) {
            Schema::table('penduduks', function (Blueprint $table) {
                $table->dropForeign(['kartu_keluarga_id']);
                $table->dropColumn('kartu_keluarga_id');
            });
        }
    }
};

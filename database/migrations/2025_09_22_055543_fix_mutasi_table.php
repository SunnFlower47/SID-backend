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
        Schema::table('mutasis', function (Blueprint $table) {
            $table->string('kategori_mutasi', 100)->change();
        });

        if (!Schema::hasColumn('penduduks', 'status')) {
            Schema::table('penduduks', function (Blueprint $table) {
                $table->string('status', 20)->default('aktif')->after('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasis', function (Blueprint $table) {
            $table->string('kategori_mutasi', 50)->change();
        });

        Schema::table('penduduks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

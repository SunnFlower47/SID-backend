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
        $tables = [
            'penduduks',
            'kartu_keluargas',
            'fasilitas_desas',
            'struktur_desas',
            'kontak_desas',
            'umkms',
            'testimonis'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('rt_id')->nullable()->after('id');
                    $table->foreignId('rw_id')->nullable()->after('rt_id');
                    $table->foreignId('dusun_id')->nullable()->after('rw_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'penduduks',
            'kartu_keluargas',
            'fasilitas_desas',
            'struktur_desas',
            'kontak_desas',
            'umkms',
            'testimonis'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['rt_id', 'rw_id', 'dusun_id']);
                });
            }
        }
    }
};

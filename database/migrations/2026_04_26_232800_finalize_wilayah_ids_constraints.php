<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
                // 1. Drop old columns if they exist
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'rt')) {
                        $table->dropColumn('rt');
                    }
                    if (Schema::hasColumn($tableName, 'rw')) {
                        $table->dropColumn('rw');
                    }
                    if (Schema::hasColumn($tableName, 'dusun')) {
                        $table->dropColumn('dusun');
                    }
                });

                // 2. Add Foreign Keys if they don't exist
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $dbName = config('database.connections.mysql.database');
                    
                    $this->addForeignKeyIfNotExists($tableName, 'rt_id', 'rts', $dbName);
                    $this->addForeignKeyIfNotExists($tableName, 'rw_id', 'rws', $dbName);
                    $this->addForeignKeyIfNotExists($tableName, 'dusun_id', 'dusuns', $dbName);
                });
            }
        }
    }

    private function addForeignKeyIfNotExists($tableName, $columnName, $refTable, $dbName)
    {
        $fkName = "{$tableName}_{$columnName}_foreign";
        
        $exists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$dbName, $tableName, $fkName]);

        if (empty($exists)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName, $refTable) {
                $table->foreign($columnName)->references('id')->on($refTable)->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Manual rollback if needed
    }
};

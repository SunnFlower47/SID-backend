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
        // Update RT/RW format di tabel kartu_keluargas
        // Update RT/RW format di tabel kartu_keluargas
        if (DB::getDriverName() !== 'sqlite' && Schema::hasTable('kartu_keluargas')) {
            DB::statement("
                UPDATE kartu_keluargas
                SET rt = LPAD(CAST(rt AS UNSIGNED), 3, '0'),
                    rw = LPAD(CAST(rw AS UNSIGNED), 3, '0')
                WHERE rt IS NOT NULL AND rw IS NOT NULL
            ");

            // Update dusun berdasarkan RT
            DB::statement("
                UPDATE kartu_keluargas
                SET dusun = CASE
                    WHEN rt IN ('001', '002', '003', '004', '007', '008') THEN 'Dusun Satu'
                    WHEN rt IN ('005', '006', '009', '010') THEN 'Dusun Dua'
                    ELSE dusun
                END
                WHERE rt IS NOT NULL
            ");
        }

        // Update RT/RW format di tabel penduduks jika ada
        if (DB::getDriverName() !== 'sqlite' && Schema::hasTable('penduduks') && Schema::hasColumn('penduduks', 'rt')) {
            DB::statement("
                UPDATE penduduks
                SET rt = LPAD(CAST(rt AS UNSIGNED), 3, '0'),
                    rw = LPAD(CAST(rw AS UNSIGNED), 3, '0')
                WHERE rt IS NOT NULL AND rw IS NOT NULL
            ");

            // Update dusun di tabel penduduks
            DB::statement("
                UPDATE penduduks
                SET dusun = CASE
                    WHEN rt IN ('001', '002', '003', '004', '007', '008') THEN 'Dusun Satu'
                    WHEN rt IN ('005', '006', '009', '010') THEN 'Dusun Dua'
                    ELSE dusun
                END
                WHERE rt IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan format RT/RW ke format lama (tanpa leading zero)
        DB::statement("
            UPDATE kartu_keluargas
            SET rt = CAST(rt AS UNSIGNED),
                rw = CAST(rw AS UNSIGNED)
            WHERE rt IS NOT NULL AND rw IS NOT NULL
        ");

        if (Schema::hasTable('penduduks') && Schema::hasColumn('penduduks', 'rt')) {
            DB::statement("
                UPDATE penduduks
                SET rt = CAST(rt AS UNSIGNED),
                    rw = CAST(rw AS UNSIGNED)
                WHERE rt IS NOT NULL AND rw IS NOT NULL
            ");
        }
    }
};

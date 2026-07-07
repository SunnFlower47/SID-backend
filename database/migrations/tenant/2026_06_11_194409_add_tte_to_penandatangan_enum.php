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
        DB::statement("ALTER TABLE surat_pengajuans MODIFY COLUMN penandatangan ENUM('kepala_desa', 'sekretaris_desa', 'tte') DEFAULT 'kepala_desa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting might cause data truncation if 'tte' exists, so we leave it as is or update the rows first
        // DB::statement("UPDATE surat_pengajuans SET penandatangan = 'kepala_desa' WHERE penandatangan = 'tte'");
        // DB::statement("ALTER TABLE surat_pengajuans MODIFY COLUMN penandatangan ENUM('kepala_desa', 'sekretaris_desa') DEFAULT 'kepala_desa'");
    }
};

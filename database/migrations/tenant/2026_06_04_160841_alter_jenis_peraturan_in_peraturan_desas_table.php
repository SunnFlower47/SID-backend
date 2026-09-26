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
        // Ubah jenis_peraturan dari enum menjadi varchar agar lebih fleksibel
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE peraturan_desas MODIFY jenis_peraturan VARCHAR(255) NOT NULL DEFAULT 'Peraturan Desa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke enum aslinya jika rollback
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE peraturan_desas MODIFY jenis_peraturan ENUM('APBDes', 'Perubahan APBDes', 'Lpj APBDes', 'Lainnya') NOT NULL DEFAULT 'APBDes'");
    }
};

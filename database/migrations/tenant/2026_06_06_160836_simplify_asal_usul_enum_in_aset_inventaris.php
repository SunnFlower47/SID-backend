<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update data lama
        DB::statement("UPDATE aset_inventaris SET asal_usul = 'Sumbangan' WHERE asal_usul IN ('Aset Asli Desa', 'Lainnya')");

        // 2. Ubah struktur kolom ENUM menjadi 5 opsi baku
        // Karena MariaDB/MySQL sering bermasalah mengubah ENUM via doctrine/dbal, kita gunakan raw SQL.
        DB::statement("ALTER TABLE aset_inventaris MODIFY asal_usul ENUM('APBDes', 'Bantuan Pusat', 'Bantuan Provinsi', 'Bantuan Kab/Kota', 'Sumbangan') DEFAULT 'APBDes' NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE aset_inventaris MODIFY asal_usul ENUM('APBDes', 'Bantuan Pusat', 'Bantuan Provinsi', 'Bantuan Kab/Kota', 'Sumbangan', 'Aset Asli Desa', 'Lainnya') DEFAULT 'APBDes' NULL");
    }
};

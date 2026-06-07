<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // nup dan alasan_kurang sudah ada di database, jadi kita hanya perlu menambah kondisi di aset_mutasi
        Schema::table('aset_mutasi', function (Blueprint $table) {
            if (!Schema::hasColumn('aset_mutasi', 'kondisi')) {
                $table->string('kondisi', 50)->nullable()->after('keterangan');  // kondisi pada saat transaksi
            }
        });
    }

    public function down(): void
    {
        Schema::table('aset_mutasi', function (Blueprint $table) {
            if (Schema::hasColumn('aset_mutasi', 'kondisi')) {
                $table->dropColumn('kondisi');
            }
        });
    }
};

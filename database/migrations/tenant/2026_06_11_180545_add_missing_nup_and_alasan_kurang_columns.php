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
        // Menambahkan kolom nup di aset_inventaris jika belum ada
        Schema::table('aset_inventaris', function (Blueprint $table) {
            if (!Schema::hasColumn('aset_inventaris', 'nup')) {
                $table->string('nup', 50)->nullable()->after('aset_barang_id')->comment('Nomor Urut Pendaftaran');
            }
        });

        // Menambahkan kolom alasan_kurang di aset_mutasi jika belum ada
        Schema::table('aset_mutasi', function (Blueprint $table) {
            if (!Schema::hasColumn('aset_mutasi', 'alasan_kurang')) {
                $table->string('alasan_kurang', 100)->nullable()->after('jenis')->comment('Alasan pengurangan aset (dijual, dihibahkan, rusak, dll)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aset_inventaris', function (Blueprint $table) {
            if (Schema::hasColumn('aset_inventaris', 'nup')) {
                $table->dropColumn('nup');
            }
        });

        Schema::table('aset_mutasi', function (Blueprint $table) {
            if (Schema::hasColumn('aset_mutasi', 'alasan_kurang')) {
                $table->dropColumn('alasan_kurang');
            }
        });
    }
};

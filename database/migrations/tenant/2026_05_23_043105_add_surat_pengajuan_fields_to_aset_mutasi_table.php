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
        Schema::table('aset_mutasi', function (Blueprint $table) {
            $table->unsignedBigInteger('berita_acara_surat_id')->nullable()->after('keterangan');
            $table->unsignedBigInteger('sk_surat_id')->nullable()->after('berita_acara_surat_id');

            $table->foreign('berita_acara_surat_id')
                  ->references('id')
                  ->on('surat_pengajuans')
                  ->onDelete('set null');

            $table->foreign('sk_surat_id')
                  ->references('id')
                  ->on('surat_pengajuans')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aset_mutasi', function (Blueprint $table) {
            $table->dropForeign(['berita_acara_surat_id']);
            $table->dropForeign(['sk_surat_id']);
            $table->dropColumn(['berita_acara_surat_id', 'sk_surat_id']);
        });
    }
};

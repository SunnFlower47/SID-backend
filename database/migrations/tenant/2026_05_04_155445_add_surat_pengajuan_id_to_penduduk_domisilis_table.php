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
        Schema::table('penduduk_domisilis', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_pengajuan_id')->nullable()->after('nomor_surat');
            $table->foreign('surat_pengajuan_id')->references('id')->on('surat_pengajuans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduk_domisilis', function (Blueprint $table) {
            $table->dropForeign(['surat_pengajuan_id']);
            $table->dropColumn('surat_pengajuan_id');
        });
    }
};

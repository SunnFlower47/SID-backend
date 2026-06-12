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
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable()->change();
            $table->string('nomor_resi')->nullable()->unique()->after('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable(false)->change();
            $table->dropColumn('nomor_resi');
        });
    }
};

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
        Schema::table('histori_pengeluarans', function (Blueprint $table) {
            $table->decimal('pajak_ppn', 15, 2)->default(0)->after('jumlah');
            $table->decimal('pajak_pph21', 15, 2)->default(0)->after('pajak_ppn');
            $table->decimal('pajak_pph22', 15, 2)->default(0)->after('pajak_pph21');
            $table->decimal('pajak_pph23', 15, 2)->default(0)->after('pajak_pph22');
            $table->date('tanggal_setor_pajak')->nullable()->after('pajak_pph23');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histori_pengeluarans', function (Blueprint $table) {
            $table->dropColumn(['pajak_ppn', 'pajak_pph21', 'pajak_pph22', 'pajak_pph23', 'tanggal_setor_pajak']);
        });
    }
};

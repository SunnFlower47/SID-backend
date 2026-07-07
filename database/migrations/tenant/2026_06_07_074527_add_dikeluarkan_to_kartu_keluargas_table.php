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
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            $table->string('tempat_dikeluarkan', 100)->nullable()->after('alamat');
            $table->date('tanggal_dikeluarkan')->nullable()->after('tempat_dikeluarkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            $table->dropColumn(['tempat_dikeluarkan', 'tanggal_dikeluarkan']);
        });
    }
};

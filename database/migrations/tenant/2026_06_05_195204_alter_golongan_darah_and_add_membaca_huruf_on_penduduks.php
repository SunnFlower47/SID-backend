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
        Schema::table('penduduks', function (Blueprint $table) {
            $table->string('golongan_darah', 20)->nullable()->change();
            $table->string('dapat_membaca_huruf', 25)->nullable()->after('pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->string('golongan_darah', 5)->nullable()->change();
            $table->dropColumn('dapat_membaca_huruf');
        });
    }
};

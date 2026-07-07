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
        Schema::table('penduduk_domisilis', function (Blueprint $blueprint) {
            $blueprint->string('status_perkawinan', 50)->nullable()->after('agama');
            $blueprint->string('kewarganegaraan', 50)->default('Indonesia')->after('status_perkawinan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduk_domisilis', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['status_perkawinan', 'kewarganegaraan']);
        });
    }
};

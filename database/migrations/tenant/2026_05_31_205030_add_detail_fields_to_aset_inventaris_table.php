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
        Schema::table('aset_inventaris', function (Blueprint $table) {
            $table->string('no_polisi', 100)->nullable()->after('keterangan');
            $table->string('no_mesin', 100)->nullable()->after('no_polisi');
            $table->string('no_rangka', 100)->nullable()->after('no_mesin');
            $table->string('no_bpkb', 100)->nullable()->after('no_rangka');
            $table->string('no_sertifikat', 100)->nullable()->after('no_bpkb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aset_inventaris', function (Blueprint $table) {
            $table->dropColumn(['no_polisi', 'no_mesin', 'no_rangka', 'no_bpkb', 'no_sertifikat']);
        });
    }
};

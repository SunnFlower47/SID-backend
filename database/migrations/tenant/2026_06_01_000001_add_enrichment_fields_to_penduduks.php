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
            $table->string('golongan_darah', 5)->nullable()->after('tanggal_lahir');
            $table->string('warganegara', 10)->nullable()->default('WNI')->after('golongan_darah');
            $table->string('no_akta_lahir', 50)->nullable()->after('warganegara');
            $table->string('status_pendidikan', 50)->nullable()->after('pendidikan');
            $table->string('telepon', 20)->nullable()->after('nama_ibu');
            $table->string('cacat_type', 50)->nullable()->after('keterangan');
            $table->string('sakit_menahun', 100)->nullable()->after('cacat_type');
            $table->string('status_asuransi', 50)->nullable()->after('sakit_menahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->dropColumn([
                'golongan_darah',
                'warganegara',
                'no_akta_lahir',
                'status_pendidikan',
                'telepon',
                'cacat_type',
                'sakit_menahun',
                'status_asuransi'
            ]);
        });
    }
};

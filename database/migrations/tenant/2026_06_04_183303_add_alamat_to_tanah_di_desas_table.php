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
        Schema::table('tanah_di_desas', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('nama_pemilik');
            $table->string('no_sertifikat')->nullable()->after('alamat');
            $table->string('batas_utara')->nullable()->after('no_sertifikat');
            $table->string('batas_timur')->nullable()->after('batas_utara');
            $table->string('batas_selatan')->nullable()->after('batas_timur');
            $table->string('batas_barat')->nullable()->after('batas_selatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanah_di_desas', function (Blueprint $table) {
            $table->dropColumn([
                'alamat', 
                'no_sertifikat', 
                'batas_utara', 
                'batas_timur', 
                'batas_selatan', 
                'batas_barat'
            ]);
        });
    }
};

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
            // 1. Lepas Foreign Key-nya dulu
            $table->dropForeign(['rt_id']);
            $table->dropForeign(['rw_id']);
            $table->dropForeign(['dusun_id']);

            // 2. Drop kolomnya
            $table->dropColumn(['rt_id', 'rw_id', 'dusun_id']);
            
            // 3. Drop kolom legacy lainnya
            if (Schema::hasColumn('penduduks', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('penduduks', 'nkk')) {
                $table->dropColumn('nkk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->foreignId('rt_id')->nullable()->constrained('rts');
            $table->foreignId('rw_id')->nullable()->constrained('rws');
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns');
            $table->text('alamat')->nullable();
        });
    }
};

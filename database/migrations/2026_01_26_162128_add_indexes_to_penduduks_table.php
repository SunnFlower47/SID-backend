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
            $table->index('nkk');
            $table->index('nama');
            $table->index('rt');
            $table->index('rw');
            $table->index('dusun');
            // Compound index for frequent filtering
            $table->index(['rt', 'rw', 'dusun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->dropIndex(['nkk']);
            $table->dropIndex(['nama']);
            $table->dropIndex(['rt']);
            $table->dropIndex(['rw']);
            $table->dropIndex(['dusun']);
            $table->dropIndex(['rt', 'rw', 'dusun']);
        });
    }
};

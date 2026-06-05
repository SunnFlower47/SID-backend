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
        Schema::table('kader_pemberdayaans', function (Blueprint $table) {
            $table->string('rt')->nullable()->after('alamat');
            $table->string('rw')->nullable()->after('rt');
            $table->string('dusun')->nullable()->after('rw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kader_pemberdayaans', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw', 'dusun']);
        });
    }
};

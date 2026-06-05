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
            $table->string('nik', 16)->unique()->nullable()->after('id');
            $table->string('no_hp', 20)->nullable()->after('umur');
            $table->string('email')->nullable()->after('no_hp');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif')->after('keterangan');
            $table->string('foto')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kader_pemberdayaans', function (Blueprint $table) {
            $table->dropColumn(['nik', 'no_hp', 'email', 'status', 'foto']);
        });
    }
};

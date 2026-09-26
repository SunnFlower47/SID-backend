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
        Schema::table('proyek_desas', function (Blueprint $table) {
            $table->unsignedBigInteger('apbdes_id')->nullable()->after('id');
            $table->foreign('apbdes_id')->references('id')->on('apbdes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek_desas', function (Blueprint $table) {
            $table->dropForeign(['apbdes_id']);
            $table->dropColumn('apbdes_id');
        });
    }
};

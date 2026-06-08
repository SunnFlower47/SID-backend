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
            $table->string('volume')->nullable()->after('deskripsi');
            $table->text('sasaran')->nullable()->after('volume');
            $table->enum('sifat_proyek', ['baru', 'lanjutan'])->default('baru')->after('sasaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek_desas', function (Blueprint $table) {
            $table->dropColumn(['volume', 'sasaran', 'sifat_proyek']);
        });
    }
};

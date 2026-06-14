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
        Schema::rename('wilayah_import_conflicts', 'import_conflicts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('import_conflicts', 'wilayah_import_conflicts');
    }
};

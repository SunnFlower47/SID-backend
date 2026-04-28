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
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            $table->string('file_lampiran')->nullable()->after('data_tambahan');
            $table->timestamp('processed_at')->nullable()->after('admin_id');
        });

        // Map existing statuses to new ones before changing enum
        DB::table('surat_pengajuans')->where('status', 'approved')->update(['status' => 'pending']); // temporarily move approved to pending to avoid enum error
        
        // Actually, let's just modify the enum. DBs usually allow adding values.
        // But to be safe and clean:
        DB::statement("ALTER TABLE surat_pengajuans MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending'");
        
        // Update data
        DB::table('surat_pengajuans')->where('status', 'approved')->update(['status' => 'diproses']);
        DB::table('surat_pengajuans')->where('status', 'completed')->update(['status' => 'selesai']);
        DB::table('surat_pengajuans')->where('status', 'rejected')->update(['status' => 'ditolak']);
        
        // Finalize enum to only new values
        DB::statement("ALTER TABLE surat_pengajuans MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['file_lampiran', 'processed_at']);
        });
        
        DB::statement("ALTER TABLE surat_pengajuans MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};

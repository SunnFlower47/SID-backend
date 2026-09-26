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
            // Hapus foreign key constraint dulu
            $table->dropForeign(['created_by']);
            
            // Hapus kolom yang tidak perlu untuk pengajuan warga
            $table->dropColumn(['surat_type', 'created_by']);
            
            // Tambah kolom untuk data pengaju (warga yang mengajukan)
            $table->string('nik_pengaju')->after('id');
            $table->string('nama_pengaju')->after('nik_pengaju');
            $table->string('email_pengaju')->nullable()->after('nama_pengaju');
            $table->string('no_hp_pengaju')->nullable()->after('email_pengaju');
            
            // Tambah kolom untuk admin yang approve
            $table->string('keterangan_admin')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengajuans', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn(['nik_pengaju', 'nama_pengaju', 'email_pengaju', 'no_hp_pengaju', 'keterangan_admin']);
            
            // Kembalikan kolom yang dihapus
            $table->string('surat_type');
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Kembalikan foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};

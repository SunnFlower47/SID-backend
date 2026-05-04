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
        Schema::create('struktur_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->enum('kategori', [
                'kepala_desa', 'sekretaris', 'bendahara', 
                'kasi_pemerintahan', 'kasi_kesejahteraan', 'kasi_pelayanan', 
                'kaur_tata_usaha', 'kaur_keuangan', 'kaur_perencanaan',
                'kepala_dusun', 'ketua_rw', 'ketua_rt', 'ketua_bumdes', 'staf', 'lainnya'
            ]);
            $table->string('nik')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
            
            $table->text('tugas_wewenang')->nullable();
            $table->date('tanggal_pengangkatan')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struktur_desas');
    }
};

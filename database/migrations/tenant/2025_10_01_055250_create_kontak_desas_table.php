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
        Schema::create('kontak_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['kantor_desa', 'kepala_desa', 'sekretaris', 'bendahara', 'kasi_pemerintahan', 'kasi_kesejahteraan', 'kasi_pelayanan', 'kepala_dusun', 'ketua_rw', 'ketua_rt', 'ketua_bumdes', 'puskesmas', 'posyandu', 'sekolah', 'masjid', 'lainnya']);
            $table->string('jabatan')->nullable();
            $table->string('alamat');
            
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
            
            $table->string('no_telepon')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('jam_operasional')->nullable();
            $table->text('deskripsi')->nullable();
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
        Schema::dropIfExists('kontak_desas');
    }
};

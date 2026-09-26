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
        Schema::create('fasilitas_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['sekolah', 'posyandu', 'masjid', 'gereja', 'puskesmas', 'pos_ronda', 'balai_desa', 'lapangan', 'pasar', 'lainnya']);
            $table->text('alamat');
            
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
            
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('kontak')->nullable();
            $table->string('jam_operasional')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas_desas');
    }
};

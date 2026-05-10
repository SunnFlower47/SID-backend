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
        Schema::create('master_jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->boolean('is_struktur')->default(true);
            $table->boolean('is_kontak')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Seed data awal biar admin nggak repot input dari nol
        $initialData = [
            // Jabatan Struktur & Kontak
            ['nama' => 'Kepala Desa', 'slug' => 'kepala_desa', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 1],
            ['nama' => 'Sekretaris Desa', 'slug' => 'sekretaris', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 2],
            ['nama' => 'Bendahara Desa', 'slug' => 'bendahara', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 3],
            ['nama' => 'Kasi Pemerintahan', 'slug' => 'kasi_pemerintahan', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 4],
            ['nama' => 'Kasi Kesejahteraan', 'slug' => 'kasi_kesejahteraan', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 5],
            ['nama' => 'Kasi Pelayanan', 'slug' => 'kasi_pelayanan', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 6],
            ['nama' => 'Kepala Dusun', 'slug' => 'kepala_dusun', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 7],
            ['nama' => 'Ketua RW', 'slug' => 'ketua_rw', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 8],
            ['nama' => 'Ketua RT', 'slug' => 'ketua_rt', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 9],
            ['nama' => 'Ketua BUMDes', 'slug' => 'ketua_bumdes', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 10],
            
            // Fasilitas Umum (Kontak Saja)
            ['nama' => 'Kantor Desa', 'slug' => 'kantor_desa', 'is_struktur' => false, 'is_kontak' => true, 'urutan' => 11],
            ['nama' => 'Puskesmas', 'slug' => 'puskesmas', 'is_struktur' => false, 'is_kontak' => true, 'urutan' => 12],
            ['nama' => 'Posyandu', 'slug' => 'posyandu', 'is_struktur' => false, 'is_kontak' => true, 'urutan' => 13],
            ['nama' => 'Sekolah', 'slug' => 'sekolah', 'is_struktur' => false, 'is_kontak' => true, 'urutan' => 14],
            ['nama' => 'Masjid', 'slug' => 'masjid', 'is_struktur' => false, 'is_kontak' => true, 'urutan' => 15],
            ['nama' => 'Lainnya', 'slug' => 'lainnya', 'is_struktur' => true, 'is_kontak' => true, 'urutan' => 99],
        ];

        foreach ($initialData as $data) {
            \Illuminate\Support\Facades\DB::table('master_jabatans')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jabatans');
    }
};

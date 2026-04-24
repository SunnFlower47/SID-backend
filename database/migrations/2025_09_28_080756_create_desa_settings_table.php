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
        Schema::create('desa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, json
            $table->string('group')->default('general'); // general, surat, logo
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('desa_settings')->insert([
            // General Desa Info
            ['key' => 'nama_desa', 'value' => 'Desa Cibatu', 'type' => 'text', 'group' => 'general', 'description' => 'Nama Desa'],
            ['key' => 'kecamatan', 'value' => 'Cibatu', 'type' => 'text', 'group' => 'general', 'description' => 'Nama Kecamatan'],
            ['key' => 'kabupaten', 'value' => 'Purwakarta', 'type' => 'text', 'group' => 'general', 'description' => 'Nama Kabupaten'],
            ['key' => 'provinsi', 'value' => 'Jawa Barat', 'type' => 'text', 'group' => 'general', 'description' => 'Nama Provinsi'],
            ['key' => 'kode_pos', 'value' => '41161', 'type' => 'text', 'group' => 'general', 'description' => 'Kode Pos'],
            ['key' => 'alamat_lengkap', 'value' => 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161', 'type' => 'text', 'group' => 'general', 'description' => 'Alamat Lengkap Desa'],
            ['key' => 'telepon', 'value' => '(0262) 123456', 'type' => 'text', 'group' => 'general', 'description' => 'Nomor Telepon'],
            ['key' => 'email', 'value' => 'desacibatu.2001@gmail.com', 'type' => 'text', 'group' => 'general', 'description' => 'Email Desa'],
            ['key' => 'website', 'value' => 'https://desa-cibatu.id', 'type' => 'text', 'group' => 'general', 'description' => 'Website Desa'],

            // Kepala Desa Info
            ['key' => 'nama_kepala_desa', 'value' => 'H. MAMAN SUTARMAN, S.Pd.I', 'type' => 'text', 'group' => 'kepala_desa', 'description' => 'Nama Kepala Desa'],
            ['key' => 'nip_kepala_desa', 'value' => '19651231 199003 1 001', 'type' => 'text', 'group' => 'kepala_desa', 'description' => 'NIP Kepala Desa'],
            ['key' => 'jabatan_kepala_desa', 'value' => 'Kepala Desa Cibatu', 'type' => 'text', 'group' => 'kepala_desa', 'description' => 'Jabatan Kepala Desa'],

            // Sekretaris Desa Info
            ['key' => 'nama_sekretaris', 'value' => 'Drs. BUDIMAN, M.Si', 'type' => 'text', 'group' => 'sekretaris', 'description' => 'Nama Sekretaris Desa'],
            ['key' => 'nip_sekretaris', 'value' => '19700315 199203 1 002', 'type' => 'text', 'group' => 'sekretaris', 'description' => 'NIP Sekretaris Desa'],
            ['key' => 'jabatan_sekretaris', 'value' => 'Sekretaris Desa Cibatu', 'type' => 'text', 'group' => 'sekretaris', 'description' => 'Jabatan Sekretaris Desa'],

            // Logo dan Branding
            ['key' => 'logo_desa', 'value' => null, 'type' => 'image', 'group' => 'logo', 'description' => 'Logo Desa'],
            ['key' => 'logo_kabupaten', 'value' => null, 'type' => 'image', 'group' => 'logo', 'description' => 'Logo Kabupaten'],
            ['key' => 'logo_provinsi', 'value' => null, 'type' => 'image', 'group' => 'logo', 'description' => 'Logo Provinsi'],

            // Surat Settings
            ['key' => 'format_nomor_surat', 'value' => '{kode_surat}/{nomor_urut}/{bulan}/{tahun}', 'type' => 'text', 'group' => 'surat', 'description' => 'Format Nomor Surat'],
            ['key' => 'kode_surat_domisili', 'value' => 'SKD', 'type' => 'text', 'group' => 'surat', 'description' => 'Kode Surat Domisili'],
            ['key' => 'kode_surat_pengantar', 'value' => 'SP', 'type' => 'text', 'group' => 'surat', 'description' => 'Kode Surat Pengantar'],
            ['key' => 'kode_surat_pindah', 'value' => 'SKP', 'type' => 'text', 'group' => 'surat', 'description' => 'Kode Surat Pindah'],
            ['key' => 'kode_surat_kematian', 'value' => 'SKK', 'type' => 'text', 'group' => 'surat', 'description' => 'Kode Surat Kematian'],
            ['key' => 'kode_surat_kelahiran', 'value' => 'SKKL', 'type' => 'text', 'group' => 'surat', 'description' => 'Kode Surat Kelahiran'],
            ['key' => 'kode_surat_tidak_mampu', 'value' => 'SKTM', 'type' => 'text', 'group' => 'surat', 'description' => 'Kode Surat Tidak Mampu'],

            // Template Settings
            ['key' => 'template_header', 'value' => 'PEMERINTAH KABUPATEN GARUT\nKECAMATAN CIBATU\nDESA CIBATU', 'type' => 'text', 'group' => 'template', 'description' => 'Template Header Surat'],
            ['key' => 'template_footer', 'value' => 'Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.', 'type' => 'text', 'group' => 'template', 'description' => 'Template Footer Surat'],

            // Created and updated timestamps
            ['key' => 'created_at', 'value' => now(), 'type' => 'datetime', 'group' => 'system', 'description' => 'Tanggal Dibuat'],
            ['key' => 'updated_at', 'value' => now(), 'type' => 'datetime', 'group' => 'system', 'description' => 'Tanggal Diupdate']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desa_settings');
    }
};

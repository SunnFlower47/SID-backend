Fitur Sub-Template Surat (Multi-Template per Jenis Surat)
Latar Belakang
Banyak jenis surat memiliki lebih dari satu dokumen yang perlu dicetak:

Surat Nikah: N1, N2, N4, N5, N6 (cerai mati), Surat Keterangan Wali
Surat Keterangan: Keterangan Umum, Tidak Mampu, Keterangan Usaha, Domisili Usaha, dll
Sistem yang ada sekarang hanya mendukung 1 template Word dan 1 form_json per jenis surat. Fitur ini menambahkan kemampuan sub-template yang bersifat general — surat apapun bisa punya banyak template dan form masing-masing.

Konsep Arsitektur Form JSON
Aturan Sederhana:
Kondisi	Perilaku
surat_types.form_json ada, tanpa sub-template ID	Global Form — tampil untuk semua, field dipakai bersama
surat_type_templates.form_json ada	Form Spesifik — tampil dengan judul section nama sub-template
Keduanya ada	Global tampil duluan, lalu section per sub-template di bawah
Contoh tampilan form saat pengajuan Surat Nikah:

┌─ DATA UMUM ──────────────────────────────────────────┐
│  (form_json global dari SuratType "Nikah")           │
│  Tanggal Rencana Nikah : [____________]              │
│  Calon Pasangan (Nama) : [____________]              │
└──────────────────────────────────────────────────────┘
┌─ DATA TAMBAHAN — N5 • Surat Izin Orang Tua ─────────┐
│  (form_json dari sub-template N5)                    │
│  Nama Ayah Kandung     : [____________]              │
│  Pekerjaan Ayah        : [____________]              │
└──────────────────────────────────────────────────────┘
┌─ DATA TAMBAHAN — N6 • Surat Keterangan Kematian ────┐
│  (form_json dari sub-template N6)                    │
│  Tanggal Kematian Pasangan : [____________]          │
│  No Akta Kematian          : [____________]          │
└──────────────────────────────────────────────────────┘
NOTE

Semua field dari semua section digabung ke dalam satu data_tambahan flat. Variabel template Word tetap langsung pakai nama field seperti biasa (misal: ${tanggal_rencana_nikah}, ${nama_ayah}).

Proposed Changes
Layer 1 — Database
[NEW] Migration: create_surat_type_templates_table
php

Schema::create('surat_type_templates', function (Blueprint $table) {
    $table->id();
    $table->string('surat_type_id');
    $table->foreign('surat_type_id')->references('id')->on('surat_types')->cascadeOnDelete();
    $table->string('kode', 20);            // N1, N2, WALI, UMUM, dll
    $table->string('nama', 150);           // Nama lengkap dokumen
    $table->text('deskripsi')->nullable();
    $table->string('file_template')->nullable();
    $table->json('form_json')->nullable(); // ← BARU: form khusus per sub-template
    $table->integer('urutan')->default(0);
    $table->boolean('is_active')->default(true);
    $table->enum('gender_filter', ['all', 'L', 'P'])->default('all');
    $table->timestamps();
});
[NEW] Migration: add_has_multi_template_to_surat_types
php

Schema::table('surat_types', function (Blueprint $table) {
    $table->boolean('has_multi_template')->default(false)->after('is_public');
});
Layer 2 — Backend Models
[NEW] app/Models/SuratTypeTemplate.php
php

protected $fillable = [
    'surat_type_id', 'kode', 'nama', 'deskripsi',
    'file_template', 'form_json', 'urutan', 'is_active', 'gender_filter'
];
protected $casts = [
    'form_json' => 'array',
    'is_active' => 'boolean',
];
Relasi belongsTo(SuratType)
Boot event: auto-delete file .docx dari storage saat dihapus/diupdate
[MODIFY] app/Models/SuratType.php
Tambah has_multi_template ke $fillable dan $casts
Tambah relasi: public function templates(): HasMany
Layer 3 — Backend Controller CRUD Sub-Template
[MODIFY] app/Http/Controllers/Tenant/Pelayanan/SuratTypeController.php
Tambah 4 method:

listTemplates(SuratType $suratType) → Return JSON: daftar sub-template beserta form_json masing-masing

storeTemplate(Request $request, SuratType $suratType)


Validasi:
- kode: required|string|max:20
- nama: required|string|max:150
- deskripsi: nullable|string
- file_template: nullable|file|mimes:docx|max:5120
- form_json: nullable|array
- urutan: nullable|integer
- gender_filter: nullable|in:all,L,P
updateTemplate(Request $request, SuratType, SuratTypeTemplate) → Sama seperti store, handle re-upload file opsional

destroyTemplate(SuratType, SuratTypeTemplate) → Hapus record + file dari storage

Layer 4 — Backend Generate Multi-Dokumen
[MODIFY] app/Services/Pelayanan/SuratPengajuanService.php
Tambah method generateMultiDocument(SuratPengajuan $pengajuan, array $templateIds):


1. Load sub-template yang dipilih (filter by templateIds, is_active)
2. Build data global via buildSuratData() + formatDataForWord()
3. Loop setiap sub-template:
   - Merge data global + data_tambahan dari request
   - generate .docx via SuratService::generate()
   - simpan path ke array $files
4. Jika count($files) === 1:
   → return response()->download($files[0])
5. Jika count($files) > 1:
   → buat ZIP di storage/app/private/generated_surat/
   → return response()->download($zipPath)
   → cleanup file .docx sementara
[MODIFY] app/Http/Controllers/Tenant/Pelayanan/SuratPengajuanController.php
Tambah generateMultiPdf(Request $request, SuratPengajuan $pengajuan):


Validasi:
- template_ids: required|array|min:1
- template_ids.*: exists:surat_type_templates,id
Layer 5 — Routes
[MODIFY] Routes
php

// Sub-template CRUD (JSON API)
Route::prefix('surat-type/{suratType}')->group(function () {
    Route::get('templates',               [SuratTypeController::class, 'listTemplates']);
    Route::post('templates',              [SuratTypeController::class, 'storeTemplate']);
    Route::post('templates/{template}',   [SuratTypeController::class, 'updateTemplate']);
    Route::delete('templates/{template}', [SuratTypeController::class, 'destroyTemplate']);
});
// Multi-dokumen download
Route::post('surat-pengajuan/{suratPengajuan}/generate-multi',
    [SuratPengajuanController::class, 'generateMultiPdf'])
    ->name('admin.surat-pengajuan.generate-multi');
Layer 6 — Frontend: Master Surat Form
[MODIFY] resources/js/Pages/Tenant/SuratType/Form.jsx
Tambah section baru di bawah form utama:

Toggle:

☑ Surat ini memiliki beberapa template (sub-template)
Jika toggle aktif — tabel sub-template:

#	Kode	Nama Dokumen	Gender	Form Khusus	File	Aktif	Aksi
1	N1	Surat Keterangan Nikah	Semua	—	✅	✅	Edit · Hapus
2	N5	Surat Izin Orang Tua	Semua	2 field	✅	✅	Edit · Hapus
3	WALI	Surat Keterangan Wali	Perempuan	1 field	✅	✅	Edit · Hapus
Tombol "+ Tambah Sub-Template" → buka form inline dengan:

Input: Kode, Nama, Deskripsi, Gender Filter, Urutan
Upload .docx
Builder form_json mini — sama seperti builder yang sudah ada di Form.jsx utama, tapi untuk sub-template ini saja
NOTE

form_json di SuratType (level atas) = Global Form (label: "DATA UMUM"). form_json di SuratTypeTemplate = Form Spesifik (label: nama sub-template, misal "DATA TAMBAHAN — N5").

Layer 7 — Frontend: Form Pengajuan Surat (Create/Edit)
[MODIFY] resources/js/Pages/Tenant/SuratPengajuan/Create.jsx
Saat jenis surat dipilih dan has_multi_template = true:

Tampil form dengan section berlabel:

jsx

// 1. Render global form (dari suratType.form_json)
<FormSection title="DATA UMUM">
  {renderFormFields(suratType.form_json)}
</FormSection>
// 2. Render form per sub-template yang aktif
{suratType.templates
  .filter(t => t.is_active && t.form_json?.length > 0)
  .map(t => (
    <FormSection title={`DATA TAMBAHAN — ${t.kode} • ${t.nama}`}>
      {renderFormFields(t.form_json)}
    </FormSection>
  ))
}
Semua field dimasukkan ke data_tambahan dengan key sesuai name di form_json.

Layer 8 — Frontend: Panel Cetak (Show Pengajuan)
[MODIFY] resources/js/Pages/Tenant/SuratPengajuan/Show.jsx
Jika has_multi_template = false: tombol cetak biasa, tidak berubah.

Jika has_multi_template = true: tampil panel checkbox:


┌─ Cetak Dokumen Surat Nikah ──────────────────────────┐
│                                                      │
│  Pilih dokumen yang ingin dicetak:                   │
│  ☑  N1  — Surat Keterangan Untuk Nikah               │
│  ☑  N2  — Surat Keterangan Asal Usul                 │
│  ☑  N4  — Surat Keterangan Tentang Orang Tua         │
│  ☑  N5  — Surat Izin Orang Tua / Wali                │
│  ☐  N6  — Surat Keterangan Kematian (Cerai Mati)     │
│  ☑  WALI — Surat Keterangan Wali  [👩 Khusus Wanita] │
│                                                      │
│  [Pilih Semua]  [Hapus Semua]                        │
│                                                      │
│         ▶  Cetak Dokumen Terpilih (5)                │
│             (akan didownload sebagai .zip)           │
└──────────────────────────────────────────────────────┘
Sub-template difilter gender otomatis dari data penduduk
Jumlah di tombol update real-time
1 dipilih → .docx langsung
2+ dipilih → .zip
Urutan Pengerjaan
[ ] 1. Migration: tabel surat_type_templates
[ ] 2. Migration: kolom has_multi_template di surat_types
[ ] 3. Model SuratTypeTemplate (baru)
[ ] 4. Update Model SuratType
[ ] 5. CRUD method di SuratTypeController
[ ] 6. Routes baru
[ ] 7. Method generateMultiDocument di SuratPengajuanService
[ ] 8. Method generateMultiPdf di SuratPengajuanController
[ ] 9. Frontend: SuratType/Form.jsx — section sub-template + form_json builder per sub-template
[ ] 10. Frontend: SuratPengajuan/Create.jsx — render form berlabel per sub-template
[ ] 11. Frontend: SuratPengajuan/Show.jsx — panel cetak checkbox
Verification Plan
Buat SuratType "Nikah" → aktifkan multi-template
Set global form_json = [{name: "tanggal_rencana_nikah", label: "Tanggal Rencana Nikah", type: "date"}]
Tambah sub-template N5, set form_json N5 = [{name: "nama_ayah", label: "Nama Ayah"}]
Buka form pengajuan → tampil 2 section (DATA UMUM + DATA TAMBAHAN — N5)
Isi form → simpan → buka Show
Panel cetak tampil checkbox → centang N1 + N5 → download ZIP berisi 2 file ✅
Buka surat lain (Domisili) → tombol cetak biasa ✅
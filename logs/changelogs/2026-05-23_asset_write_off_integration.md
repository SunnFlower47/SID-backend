# Changelog - 23 Mei 2026

## Integrasi Modul Penghapusan Aset (Write-off) dengan Mutasi Berkurang & Surat Pengajuan

Kami telah mengubah alur modul penghapusan aset desa. Daripada menunggu saldo kuantitas menjadi `0` di modul terpisah, penghapusan kini dilakukan secara parsial maupun penuh melalui transaksi **Mutasi Berkurang (Pengurangan Aset)**. Setiap pencatatan mutasi berkurang akan otomatis menghasilkan dokumen administrasi surat pengajuan yang diperlukan.

### Perubahan File & Struktur:

1. **Database & Migrations**:
   - `database/migrations/2026_05_23_043105_add_surat_pengajuan_fields_to_aset_mutasi_table.php` [NEW]: Menambahkan kolom `berita_acara_surat_id` dan `sk_surat_id` di tabel `aset_mutasi` dengan foreign key ke `surat_pengajuans`.

2. **Backend Models**:
   - `app/Models/AsetMutasi.php` [MODIFY]: Mendaftarkan kolom berita acara & SK ke fillable serta mendefinisikan relasi `beritaAcaraSurat()` dan `skSurat()`.
   - `app/Models/SuratPengajuan.php` [MODIFY]: Mendaftarkan tipe surat baru (`berita-acara-penghapusan-aset` dan `sk-penghapusan-aset`) di accessor model `getSuratTypeNameAttribute()`.

3. **Master Data & Seeders**:
   - `database/seeders/SuratTypeSeeder.php` [MODIFY]: Mendaftarkan data awal (master type) untuk surat berita acara (BAPA) dan surat keputusan kepala desa (SKPA) untuk penghapusan aset.

4. **Controllers & Business Logic**:
   - `app/Http/Controllers/Tenant/Aset/AsetMutasiController.php` [MODIFY]:
     - Mengubah method `store` untuk memicu auto-generation record `SuratPengajuan` BAPA & SKPA berstatus `selesai` jika jenis mutasi adalah `kurang` (berkurang).
     - Mengubah method `destroy` untuk melakukan data cleaning, yaitu otomatis menghapus surat pengajuan terkait saat record mutasi berkurang tersebut dihapus oleh admin.

5. **Blade Templates (DomPDF)**:
   - `resources/views/surat/templates/berita-acara-penghapusan-aset.blade.php` [NEW]: Layout dokumen berita acara resmi desa Cibatu.
   - `resources/views/surat/templates/sk-penghapusan-aset.blade.php` [NEW]: Layout dokumen keputusan kepala desa resmi.

6. **React Front-end**:
   - `resources/js/Pages/Tenant/Aset/Edit.jsx` [MODIFY]:
     - Menambahkan tabel riwayat mutasi aset di bagian paling bawah halaman edit.
     - Menampilkan link download PDF/BAPA dan SKPA langsung pada baris riwayat mutasi yang berkurang.
     - Menambahkan shortcut catat mutasi baru dan hapus mutasi terintegrasi.

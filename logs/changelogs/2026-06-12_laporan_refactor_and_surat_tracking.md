# Changelog: 12 Juni 2026 - Laporan Refactor & Surat Tracking

## Deskripsi
Pembaruan ini mencakup integrasi fitur pelacakan surat dengan QR Code, penyesuaian logika penomoran surat, perbaikan tampilan visual grafik (chart) pada statistik, serta standardisasi antarmuka pengguna (UI) di modul Laporan agar konsisten menggunakan komponen bersama (Shared Components).

## Fitur Baru & Peningkatan

### 1. Pelacakan Surat & QR Code Verifikasi
- **Auto-Numbering Resi:** Surat yang baru diajukan (status pending) sekarang menggunakan `nomor_resi` (format: `REQ-XXXXXX`) sebagai pengenal unik sementara.
- **Generate Nomor Surat Otomatis:** Nomor surat resmi hanya akan di-generate ketika status surat diubah menjadi `diproses` atau `selesai`.
- **Integrasi QR Code:** Menambahkan integrasi `simplesoftwareio/simple-qrcode` untuk menghasilkan QR Code yang berisi link verifikasi keaslian surat berdasarkan UUID (`qr_token`).
- **Template Word (.docx):** Penyesuaian `SuratPengajuanService` untuk me-replace variabel `${qr_code}` dengan gambar QR dan `${link_verifikasi}` dengan URL publik.

### 2. Standarisasi Modul Laporan
Seluruh sub-modul di dalam **Laporan** sekarang secara konsisten menggunakan `PageHeader` dan `FilterContainer` dari komponen Shared untuk menjaga konsistensi desain UI/UX:
- `Laporan/Berita/Index.jsx`
- `Laporan/KK/Index.jsx`
- `Laporan/Komparasi/Index.jsx`
- `Laporan/Mutasi/Index.jsx`
- `Laporan/Surat/Index.jsx`

### 3. Perbaikan Statistik Kependudukan
- **Visualisasi Recharts:** Memperbaiki layout pada `Laporan/Statistik/Index.jsx` agar sumbu Y (Y-Axis) pada `BarChart` memiliki proporsi lebar yang cukup (menggunakan `width` prop & `CustomYAxisTick`), sehingga label nama dusun/kategori tidak lagi terpotong.
- **Ekspor PDF:** Menambahkan dan memastikan fitur ekspor ke PDF (Print out) berfungsi sempurna di semua halaman laporan termasuk render ulang *canvas* grafik.

## File Terpengaruh
- `app/Actions/Surat/StoreSuratAction.php`
- `app/Services/Pelayanan/SuratPengajuanService.php`
- `app/Http/Controllers/Api/SuratPengajuanApiController.php`
- `app/Http/Controllers/ApiAdminPanel/SuratPengajuanController.php`
- `database/migrations/2026_06_11_185432_make_nomor_surat_nullable_on_surat_pengajuans.php`
- `resources/js/Pages/Tenant/Laporan/Statistik/Index.jsx`
- `resources/js/Pages/Tenant/Laporan/Berita/Index.jsx`
- `resources/js/Pages/Tenant/Laporan/KK/Index.jsx`
- `resources/js/Pages/Tenant/Laporan/Komparasi/Index.jsx`
- `resources/js/Pages/Tenant/Laporan/Mutasi/Index.jsx`
- `resources/js/Pages/Tenant/Laporan/Surat/Index.jsx`
- `docs/USER_MANUAL.md`

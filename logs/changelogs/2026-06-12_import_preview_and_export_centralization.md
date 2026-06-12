# Changelog 12 Juni 2026: Import Preview & Export Centralization

## 🌟 Fitur Baru (New Features)
- **Import Preview UMKM & Bantuan Sosial**: Menambahkan sistem pratinjau (*live preview*) sebelum data UMKM dan Bantuan Sosial diimpor. Menggunakan komponen `ImportPreview` yang menampilkan validasi data baris demi baris, dan memblokir pengiriman (*submit*) apabila terdapat data yang tidak valid.
- **Export Data Terpusat**: Merombak halaman `Export Data` menjadi pusat kontrol satu pintu untuk semua proses pengunduhan (*export*) data di tingkat desa.

## 🚀 Peningkatan (Improvements)
- **Format Excel Scientific Notation Fix**: Seluruh _controller/export class_ yang mengekspor NIK atau NKK (seperti Penduduk, UMKM, Bantuan Sosial, Pengaduan, Surat Pengajuan) telah diperbaiki untuk menyisipkan tanda kutip satu (`'`) pada NIK/NKK agar Excel tidak merubahnya menjadi bilangan *scientific notation*.
- **UI Export Data Berbasis Tab**: Halaman export kini dikategorikan menjadi 2 tab:
  1. Data Master & Layanan (Penduduk, KK, Bansos, Penerima Bansos, Pengaduan, UMKM, Surat Pengajuan, Aset)
  2. Buku Administrasi Desa (Mencakup 23 jenis Buku Administrasi Desa standar Permendagri No. 47 Tahun 2016)
- Setiap item di Buku Administrasi Desa kini dapat diekspor ke **Excel** dan **PDF** secara langsung dari menu Export tanpa harus masuk ke masing-masing halaman.
- Untuk export yang membutuhkan input seperti tahun (cth: Buku Inventaris Kekayaan), muncul *modal* cerdas untuk memasukkan tahun menggunakan SweetAlert2.

## 🐛 Perbaikan Bug (Bug Fixes)
- Memperbaiki bug `Call to a member function format() on null` pada `SuratPengajuanExport.php` ketika data `tanggal_pengajuan` kosong (`null`) atau bukan instance `Carbon`.

## 🛠 File yang Diubah (Modified Files)
1. `app/Exports/PenerimaBantuanSosialExport.php`
2. `app/Exports/PengaduanExport.php`
3. `app/Exports/SuratPengajuanExport.php`
4. `app/Exports/UmkmExport.php`
5. `resources/js/Pages/Tenant/Import/Import.jsx`
6. `resources/js/Pages/Tenant/Export/Index.jsx`

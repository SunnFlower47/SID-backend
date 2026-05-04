# Changelog - [2026-05-04]

## 🎯 Perbaikan Sistem Surat & Mutasi (Stability Update)

### 1. Modul Pelayanan (Surat Pengajuan)
- **PHPWord Template Engine**: Implementasi generator surat menggunakan template Microsoft Word (`.docx`). Mendukung pengisian placeholder otomatis dari database penduduk.
- **Flexible TTD**: Menghubungkan input pilihan penandatangan (Kades/Sekdes) dari frontend ke backend di `StoreSuratAction` agar pilihan TTD benar-benar tersimpan di database dan tampil di cetakan Word.
- **Word Template Sync**: Memastikan seluruh data tambahan (`data_tambahan`) terkirim dengan benar untuk kebutuhan pengisian placeholder pada template Word.
- **Fix Duplicate Letter**: Memperbaiki logika di `PendudukDomisiliService` agar tidak membuat dua record surat sekaligus saat pembuatan Surat Domisili.
- **Search Endpoint**: Mengoreksi endpoint pencarian penduduk di `Create.jsx` dari `/admin/penduduk/search` menjadi `/penduduk/search` untuk sinkronisasi rute.

### 2. Modul Kependudukan (Mutasi)
- **Automatic Letter Linkage**: Menghubungkan ID Surat ke dalam record Mutasi (Kematian). Hal ini memungkinkan sinkronisasi data yang lebih akurat.
- **Fix Undo Mutation**: Memperbaiki fitur **Undo** pada mutasi kematian agar otomatis menghapus Surat Keterangan Kematian yang terkait. Tidak ada lagi data "sampah" saat pembatalan mutasi.
- **Soft-Delete Resilience**: Menambahkan dukungan `withTrashed()` pada `MutasiService` dan `PendudukController` agar sistem tetap bisa memproses data penduduk yang sudah berstatus mutasi (meninggal/pindah).
- **Search Filtering**: Membatasi pencarian penduduk hanya untuk warga yang berstatus **Aktif** (mencegah pemilihan warga yang sudah meninggal/pindah untuk surat baru).

### 3. Database & Security
- **New Database Column**: Menambahkan kolom `surat_pengajuan_id` pada tabel `penduduk_domisilis` untuk mendukung relasi antar modul.
- **CSP Audio Fix**: Memperbarui *Content Security Policy* (CSP) untuk mengizinkan pemutaran audio notifikasi (base64) dengan menambahkan `media-src 'self' data:`.
- **Notification Stability**: Menambahkan *null-safe check* pada `NotificationController` untuk mencegah error 500 saat mencoba mengakses data penduduk yang sudah dihapus.

---
**Status**: ✅ Stabil & Siap Digunakan.

# Changelog v1.6.2 (2026-05-26)

## 🌟 Fitur Baru & Perbaikan
- **Domisili UI/UX Overhaul**: 
  - Mengubah tampilan detail domisili dari *Modal* menjadi *Full Page* (`Show.jsx`) untuk meningkatkan keterbacaan data yang banyak, menggunakan *Shared Components* (`PageHeader`, `FormCard`) agar konsisten dengan halaman Penduduk.
  - Memperbaiki aksi "Perpanjang" yang sebelumnya mengarahkan ke form manual menjadi proses 1-klik otomatis (langsung memanggil *endpoint* perpanjang di latar belakang).
  - Merapikan struktur *header* profil di halaman Detail Domisili, mengubah tombol *Edit Data*, dan memformat tanggal *ISO string* menjadi format Bahasa Indonesia (contoh: "21 Maret 2020").
- **Kependudukan & Surat**:
  - Mengurutkan hasil ekspor *Excel* Kartu Keluarga berdasarkan RT, RW, Dusun, lalu Nama Kepala Keluarga.
  - Membersihkan fitur duplikasi variabel di sistem cetak surat: variabel khusus domisili dipersingkat dari prefix `domisili_` menjadi `dm_` (misalnya `dm_alamat`), dan menghapus `dm_nomor_surat` karena sudah di-cover oleh variabel global `nomor_surat`.
- **Manajemen Storage**:
  - Menambahkan *artisan command* `surat:clean-templates` untuk memfasilitasi pembersihan file-file *template* Word yatim piatu (*orphan files*) yang sudah tidak terpakai/terhubung ke *database*.
  - Menambahkan "Storage Info Widget" pada halaman *Index* Master Surat untuk memonitor kapasitas direktori penyimpanan template secara visual (Total File Aktif, File Tidak Terpakai, dan Ukuran Storage).

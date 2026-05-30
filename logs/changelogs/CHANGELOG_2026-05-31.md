# Changelog - 31 Mei 2026

## Perbaikan Bug (Bug Fixes)
- **Global Data Hilang Saat Update**: Memperbaiki bug di mana foto/file lama terhapus (terganti menjadi null) saat mengedit data tanpa mengunggah file baru. Diimplementasikan secara massal pada controller:
  - `BeritaController`
  - `FasilitasDesaController`
  - `KontakDesaController`
  - `SuratTypeController`
- **Testimoni Route Error**: Memperbaiki error Ziggy `testimoni.update-status` dengan menyamakan definisi rute di `routes/tenant/pelayanan.php` dengan pemanggilan di sisi frontend (React). Mengubah _HTTP method_ dari POST menjadi PATCH agar lebih sesuai standar REST.
- **Testimoni Detail 500 Error**: Memperbaiki `RelationNotFoundException` saat membuka detail testimoni. Penamaan eager-load di `TestimoniController` disesuaikan dari `rt`, `rw`, `dusun` menjadi relasi yang benar: `rtMaster`, `rwMaster`, `dusunMaster`.

## Pembaruan Fitur & UI (Enhancements)
- **Konsistensi Header Detail Testimoni**: Mengganti header kustom pada halaman detail Testimoni (`Show.jsx`) menjadi komponen `PageHeader` standar agar selaras dengan tampilan menu lainnya (seperti Berita).
- **Penghapusan Relasi Wilayah pada Testimoni**: Menghilangkan input RT, RW, dan Dusun pada form Tambah Testimoni karena dianggap tidak perlu. Menghapus tampilan data wilayah tersebut di halaman detail.
- **Penghapusan Fitur Edit Testimoni**: Mencabut total kemampuan untuk mengedit testimoni guna menjaga keaslian ulasan warga. 
  - Menghapus rute `testimoni.edit` dan `testimoni.update`.
  - Menghapus fungsi `edit` dan `update` di controller.
  - Menghapus tombol Edit dari halaman antarmuka.
  - Menghapus file `Edit.jsx` secara permanen.

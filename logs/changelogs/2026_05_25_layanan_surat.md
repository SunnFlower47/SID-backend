# Changelog: Layanan Surat & Panduan Variabel
**Tanggal:** 25 Mei 2026

## ✨ Fitur Baru & Peningkatan
- **Ekstraksi Panduan Variabel Surat**: Memindahkan daftar Panduan Variabel Word dari dalam *Modal* (yang sudah terlalu penuh) menjadi Halaman (*Page*) utuh yang berdiri sendiri (`admin.surat-type.panduan`).
- **Render Variabel Kustom Dinamis**: Sistem sekarang secara otomatis memindai `form_json` dari database Master Surat dan menampilkannya sebagai variabel kustom di halaman Panduan Surat.

## 🐛 Perbaikan Bug (Bug Fixes)
- **Konflik Variabel Domisili & Penduduk**: Memperbaiki masalah bentrok variabel antara warga Domisili dan Penduduk Tetap dengan menambahkan prefix `domisili_` secara otomatis ke semua data dari tabel `penduduk_domisilis` di dalam `PendudukDomisiliService`.
- **Resolusi Wilayah & Tanggal Domisili**: Mengubah logika di `SuratPengajuanService` agar dapat menerjemahkan `rt_id`, `rw_id`, dan `dusun_id` dari data domisili menjadi teks, serta memformat `domisili_tanggal_masuk` dan `domisili_tanggal_berlaku` menjadi format tanggal Indonesia (contoh: 25 Mei 2026).
- **Variabel Surat Kematian**: Mengembalikan susunan variabel cetak Surat Kematian (`kematian_hari`, `kematian_tanggal`, `alasan`, dll) agar sesuai dengan struktur data asli yang digenerate oleh sistem **Mutasi**.
- **Layout Tombol Kembali**: Memperbaiki penempatan tombol "KEMBALI" di komponen `PageHeader` pada sub-halaman (Tambah/Edit Jenis Surat, Panduan Surat, dan Tambah/Edit Domisili) agar berada di posisi kiri atas dengan rapi, menggunakan parameter `backHref`.

## 📝 Catatan Teknis
- Tabel `penduduk_domisilis` sepenuhnya terisolasi dari tabel `penduduks`. Saat mencetak surat domisili, sistem kini mengambil data dari `data_tambahan` JSON yang sudah diformat secara mandiri, memastikan isolasi data tetap aman.

# Changelog - 5 Mei 2026

## [FITUR BARU]
- **Mesin Surat Word Dinamis:** Implementasi penuh cetak surat menggunakan template `.docx` via PHPWord.
- **Mapping Variabel Global:** Penambahan variabel `${alamat_desa}`, `${jenis_kelamin}`, `${keperluan}`, `${kecamatan}`, `${kabupaten}` yang terintegrasi dengan Profil Desa.
- **Icon Dinamis Master Surat:** Kartu pada Master Jenis Surat sekarang menampilkan icon sesuai pilihan (Lahir -> Bayi, Mati -> Tengkorak, dll).

## [PERBAIKAN / FIX]
- **Route POST Update:** Memperbaiki error 405 saat upload file template surat.
- **Path Resolution:** Sinkronisasi folder penyimpanan template surat ke `storage/app/private/templates/surat`.
- **Injeksi Data:** Proteksi terhadap error "Array to string conversion" dan penanganan format font-awesome icon ke lucide icon.

## [UI/UX IMPROVEMENTS]
- **Standardisasi Warna:** Mengubah tema warna menu Master Jenis Surat dan Profil Desa menjadi Hijau (Green) agar seragam dengan menu Penduduk.
- **Header Gradient:** Implementasi header gradient hijau yang konsisten di semua menu administratif.

---
**Status Sesi:** SELESAI (Stable)


# Changelog: Refaktor Kartu Keluarga & Optimasi Performa Wilayah
**Tanggal**: 17 Mei 2026
**Status**: COMPLETED 🚀

## Ringkasan Perubahan
Refaktor besar-besaran modul Kartu Keluarga (KK) ke arsitektur Service Layer untuk meningkatkan maintainability. Sesi ini juga mencakup optimasi gila-gilaan pada Master Wilayah yang berhasil memangkas response time dari 800ms ke ~100ms.

## Perubahan Teknis

### 1. Backend (Service Layer & Refactor)
- **KartuKeluargaService**: 
    - Migrasi total logika bisnis dari `KartuKeluargaController`.
    - Implementasi logika resolusi KK Bermasalah (Step 1 & Step 2) secara atomik.
    - Menambahkan sistem auto-cache clearing (`statsService->clearStats()`) pada setiap aksi modifikasi data.
- **Data Sanitization**: Menambahkan filter otomatis NIK/NKK (buang spasi & limit 16 karakter) sebelum masuk ke database.
- **Fix PHP 8.4 Support**: Standarisasi penulisan operator ternary untuk kompatibilitas PHP 8.4.8 (menghindari Fatal Error).

### 2. Database & Model
- **WilayahChangeLog**: Penambahan relasi `user` dan `operatorRollback` ke model User.
- **Mutasi Log**: Perbaikan error `Field kategori_mutasi doesn't have a default value` dengan mendefinisikan value default 'dalam_desa'.

## Perbaikan & Optimasi
- **Master Wilayah Cache**:
    - Implementasi **Versioned Caching** untuk dropdown RW/RT.
    - Optimasi penyimpanan Redis: data sekarang disimpan sebagai **Pure Array**, bukan Eloquent Objects, untuk performa seralisasi maksimal.
- **Log Histori Slimming**: Optimasi query log wilayah dengan mengecualikan kolom `backup_payload` (JSON raksasa) pada tampilan index.
- **Performance**: Response time navigasi Master Wilayah turun ~8x lipat lebih cepat.

## Catatan Tambahan
Modul Kependudukan sekarang sudah mengikuti standar arsitektur "Fat Service, Thin Controller", memudahkan pengembangan fitur di masa depan dan menjaga dashboard tetap responsif meski data penduduk bertambah banyak.

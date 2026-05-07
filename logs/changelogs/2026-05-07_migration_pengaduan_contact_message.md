# Changelog: Migration Pengaduan & Contact Message (v1.4.0-beta)

**Tanggal:** 2026-05-07
**Versi:** 1.4.0-beta

## Deskripsi
Migrasi besar-besaran modul **Pengaduan** dan **Pesan Kontak** dari sistem Laravel Blade lama ke arsitektur modern **React/Inertia.js**. Pembaruan ini mencakup standarisasi UI/UX sesuai dengan *Gold Standard* aplikasi SID.

## Perubahan Utama

### 1. Modul Pengaduan (Inertia Migration)
- Migrasi halaman **Index**, **Create**, **Show**, dan **Edit** ke React.
- Implementasi sistem filter yang lebih responsif.
- Standarisasi tampilan detail aduan dengan format yang lebih bersih dan profesional.

### 2. Modul Pesan Kontak (Inertia Migration & Email Feature)
- Migrasi halaman **Index** dan **Show** ke React.
- **Fitur Baru:** Integrasi sistem balasan email otomatis langsung dari panel admin.
- Penambahan Mailable `ContactMessageReply` dan template email profesional.
- Implementasi status badge: *Unread, Read, Replied,* dan *Archived*.

### 3. Standarisasi UI/UX (Gold Standard)
- Penyesuaian **Header**, **Stats Card**, dan **Filter** agar identik dengan modul **Penduduk**.
- Penggunaan skema warna yang konsisten (Green gradient, yellow icons).
- Implementasi filter *always-visible* yang lebih efisien untuk modul dengan parameter sedikit.
- Optimasi komponen statistik agar lebih informatif namun tetap ringkas (*slim design*).

## Perbaikan Teknis & Optimasi
- Pembersihan aset build melalui update `.gitignore`.
- Sinkronisasi versi aplikasi di file `VERSION` dan `package.json`.
- Perbaikan logika filter pada controller menggunakan method `filled()` untuk akurasi pencarian.
- Penghapusan `Inertia::defer` untuk meningkatkan stabilitas pemuatan data awal.

---
*Changelog ini dibuat secara otomatis untuk mencatat progres pengembangan SID.*

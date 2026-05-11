# Changelog - 2026-05-11

## Migrasi Modul UMKM, Berita & Pengumuman ke Inertia.js

### [MODUL UMKM]
- **UmkmController**: Migrasi dari Blade ke Inertia.js.
  - Implementasi relasi alias (`rt()`, `rw()`, `dusun()`) pada model UMKM untuk stabilitas data.
  - Perbaikan bug rendering foto (`foto_usaha`) dengan validasi `Array.isArray`.
- **UMKM UI (React)**:
  - `Index.jsx`, `Show.jsx`, dan `UmkmForm.jsx` dengan desain "Gold Standard".
  - Implementasi skeleton loaders untuk transisi data yang mulus.

### [MODUL BERITA & PENGUMUMAN]
- **BeritaController**: Migrasi dari Blade ke Inertia.js.
  - Implementasi `Inertia::render` dengan `Inertia::defer` untuk optimasi performa.
  - Penambahan logika pencarian (*search*) dan filter kategori/status.
  - Eager loading relasi `author` untuk data penulis konten.
- **Berita UI (React)**:
  - `Index.jsx`: Tampilan arsip informasi dengan desain premium grid.
  - `Show.jsx`: Halaman detail berita dengan fokus tipografi dan layout metadata.
  - `Create.jsx` & `Edit.jsx`: Halaman manajemen konten.
  - `BeritaStats.jsx`: Komponen statistik interaktif (Total, Published, Draft, Kategori).
  - `BeritaFilters.jsx`: Panel filter cerdas dengan debounced search.
  - `BeritaForm.jsx`: Form manajemen konten dengan preview gambar dan validasi file.

### [STANDARISASI UI]
- Menyamakan ukuran header (ikon, font, padding) dengan modul Penduduk dan UMKM.
- Menyamakan gaya tombol (Tambah, Kembali) meliputi padding, radius, dan ukuran ikon.
- Menyamakan desain *Stats Card* dan *Filter Panel* agar konsisten di seluruh aplikasi.

### [VERSI]
- Update versi aplikasi ke `1.5.0-beta` di `package.json`.

# Changelog - 08 Mei 2026

## 🚀 Migrasi Testimoni & Standarisasi UI Pusat Komunikasi

### 1. Migrasi Modul Testimoni (Blade to Inertia)
- **Pages:** `Tenant/Testimoni/` (Index, Create, Edit, Show).
- **Logic:** Penyatuan Tabel & Form ke dalam Page utama (Index.jsx) mengikuti pola modul Penduduk.
- **Controller:** Refactor `TestimoniController.php` untuk mengembalikan Inertia Response.
- **Components:** Pemisahan `TestimoniStats.jsx` dan `TestimoniFilters.jsx`.

### 2. Standarisasi Visual (Premium Green)
- **Header:** Gradasi `green-600` ke `green-800` seragam di modul Pengaduan, Pesan Masuk, dan Testimoni.
- **Ikon Header:** Diubah menjadi `text-yellow-300` (Emas) untuk kesan premium.
- **Stats Card:** Refactor `TestimoniStats.jsx` menggunakan grid dan dimensi yang identik dengan `ResidentStats` & `PengaduanStats`.

### 3. Implementasi Skeleton & Deferred
- **Optimasi:** Seluruh modul Pusat Komunikasi (Pengaduan, Pesan Masuk, Testimoni) kini menggunakan komponen `<Deferred>` dari Inertia v2.
- **Fallback:** Menggunakan `SkeletonStats` dan `SkeletonTable` dari `@/Components/Shared/Skeleton` untuk pengalaman loading yang konsisten.

### 4. Perbaikan Lainnya
- **Branding Email:** Kustomisasi template email global (vendor mail) menggunakan tema hijau Desa Cibatu (Green 800-900).
- **Pagination:** Sinkronisasi import ke `@/Components/Shared/Pagination`.
- **UI Table:** Penambahan header tabel gradasi dan badge total data.
- **Bug:** Fix error `Failed to fetch dynamically imported module` pada route Testimoni.

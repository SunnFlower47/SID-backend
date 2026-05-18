# Changelog — 2026-05-19
## UI Standardization: Import Conflicts, MasterWilayah & All CRUD Menus

---

### Scope
Standardisasi tampilan semua halaman Index admin agar konsisten dengan **Penduduk/Index.jsx** sebagai gold standard.

---

### 1. Import/Index.jsx (baru — rename dari Conflicts.jsx)

| Item | Sebelum | Sesudah |
|---|---|---|
| Nama file | `Conflicts.jsx` | `Index.jsx` |
| Inertia render path | `Tenant/Import/Index` | ✅ sama |
| Header warna | Merah/rose | **Hijau** (`from-green-600 via-green-700 to-green-800`) |
| Header icon size | `w-10 h-10` | `w-12/h-12 sm:w-14/h-14` |
| Header layout breakpoint | `sm:flex-row` | `lg:flex-row` |
| Stat cards | Tidak ada | ✅ 4 stat cards (Total, Menunggu, Ditangani, Berhasil) |
| Skeleton loading | Inline `animate-pulse` | ✅ `<SkeletonTable columns={4} rows={6} />` |
| Empty state | Text biasa | ✅ Lottie `noDataAnimation` |
| Tombol aksi | Pill besar dengan text | ✅ Icon-only `w-8 h-8 rounded-lg` hover flip-color |
| Back button | `window.history.back()` / route salah | ✅ Dihapus (menu standalone) |
| AuthenticatedLayout | tanpa `title` prop | ✅ `title="Import Conflicts"` |

**File backend terkait:**
- `app/Http/Controllers/Tenant/Admin/ImportConflictController.php` — Inertia render ke `Tenant/Import/Index`, kirim `stats` prop

---

### 2. MasterWilayah/Index.jsx

| Item | Sebelum | Sesudah |
|---|---|---|
| Header icon size | `w-10 h-10` + `w-5 h-5` | `w-12/h-12 sm:w-14/h-14` + `w-6/h-6 sm:w-7/h-7` |
| Header layout | `sm:flex-row sm:items-center` | `lg:flex-row lg:items-center` |
| Header subtitle margin | `mt-0.5` | `mt-1 sm:text-xs` |
| `shadow-inner shrink-0` pada icon box | ❌ | ✅ |
| Tombol Conflicts | `bg-white/10 border border-white/10` | `bg-green-500/30 border border-green-400/30` (seragam Penduduk) |
| Tombol Tambah Data | `px-4 py-2.5` kecil | `px-6 py-3 hover:scale-105` (seragam Penduduk) |
| `title` prop | ❌ | ✅ `title="Master Wilayah"` |
| Tombol aksi di tabel | `border border-gray-100`, `gap-1.5`, `w-3.5 h-3.5` | ✅ tanpa border, `gap-2`, `w-4 h-4`, `transition-colors` |
| Tombol MapPin duplikat | ❌ duplikat call `openCrudModal` | ✅ Dihapus |

---

### 3. Lottie Empty State — Ditambahkan ke Semua CRUD Menu

**Pattern standar:**
```jsx
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
const LottieComponent = Lottie?.default || Lottie;

// Dalam JSX empty state:
<div className="w-48 h-48 mx-auto">
    <LottieComponent animationData={noDataAnimation} loop={true} />
</div>
<p className="text-sm font-black text-gray-900 mt-2">Judul</p>
<p className="text-xs text-gray-500 mt-1">Deskripsi</p>
```

**File yang diupdate:**

| File | Sebelum | Sesudah |
|---|---|---|
| `Import/Index.jsx` | ✅ Sudah | ✅ |
| `Testimoni/Index.jsx` | Text biasa di `<td>` | ✅ Lottie |
| `Pelayanan/ContactMessage/Index.jsx` | Text biasa di `<td>` | ✅ Lottie |
| `StrukturDesa/Index.jsx` | Text biasa di `<td>` | ✅ Lottie + button borders cleaned |
| `Pengaduan/Index.jsx` | FileText icon (desktop + mobile) | ✅ Lottie desktop + mobile |
| `SuratPengajuan/Index.jsx` | Search icon + circle | ✅ Lottie |
| `SuratType/Index.jsx` | Search icon + circle | ✅ Lottie |

**Sudah ada sebelumnya (tidak diubah):**
- `Penduduk/Index.jsx`, `BantuanSosial/Index.jsx`, `KartuKeluarga/Index.jsx`, `Mutasi/Index.jsx`, `Umkm/Index.jsx`, `Berita/Index.jsx`, `Domisili/Index.jsx`

---

### 4. Skeleton Loading Audit

**Shared skeletons tersedia di:** `resources/js/Components/Shared/Skeleton/`
- `SkeletonStats.jsx` — untuk stat cards
- `SkeletonTable.jsx` — untuk data table
- `SkeletonChart.jsx` — untuk area chart/grafik
- `SkeletonActivity.jsx` — untuk activity log/timeline

**File yang diperbaiki:**

| File | Masalah | Fix |
|---|---|---|
| `Import/Index.jsx` | Inline `animate-pulse` divs | ✅ → `<SkeletonTable columns={4} rows={6} />` |
| `Laporan/Komparasi/Index.jsx` | Inline `animate-pulse` 3 kolom | ✅ → `<SkeletonStats count={3} />` |

**Laporan/Statistik/Index.jsx** — Beberapa Deferred pakai inline pulse untuk chart kecil. Ini acceptable karena konteksnya bukan tabel/stat card standar, sudah pakai `SkeletonChart` untuk chart besar.

---

### Assets Lottie

**Lokasi:** `resources/js/assets/lottie/`

| File | Kegunaan |
|---|---|
| `no-data-animation.json` | Empty state semua halaman data list |
| `loading-circle-animation.json` | Loading overlay (export, proses data) |
| `success-animation.json` | Feedback sukses (export berhasil, dll) |

---

### Convention yang Berlaku

1. **Warna header semua menu utama:** `from-green-600 via-green-700 to-green-800`
2. **Icon box di header:** `w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner shrink-0`
3. **Icon di dalam box:** `w-6 h-6 sm:w-7 sm:h-7 text-yellow-300`
4. **Layout header:** `flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6`
5. **Tombol aksi di tabel:** `w-8 h-8 rounded-lg bg-{color}-50 text-{color}-600 hover:bg-{color}-600 hover:text-white transition-colors`
6. **Skeleton loading:** Wajib pakai komponen shared, bukan inline `animate-pulse`
7. **Empty state:** Wajib pakai Lottie `noDataAnimation`

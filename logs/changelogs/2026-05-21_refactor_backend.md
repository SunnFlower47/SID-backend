# CHANGELOG

All notable changes to Sistem Desa Cibatu will be documented in this file.
Format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [v1.7.0-beta] — 2026-05-22

### 🏗️ Refactor Backend — Services, Requests, Controllers, Routes

Refactor besar pada struktur backend untuk memisahkan logika bisnis ke dalam
**domain-based folder structure**, mengurangi bloat controller, dan meningkatkan
keterbacaan kode. Total: **538 penambahan, 5771 penghapusan** di 44 file.

#### Services — Migrasi ke Domain Subfolder

**Dihapus** (file flat lama):
- `app/Services/BackupService.php`
- `app/Services/ImportConflictService.php`
- `app/Services/ImportService.php`
- `app/Services/KartuKeluargaService.php`
- `app/Services/MutasiService.php`
- `app/Services/PendudukDomisiliService.php`
- `app/Services/PendudukService.php`
- `app/Services/SuratService.php`
- `app/Services/VillageStatisticsService.php`

**Ditambah** (terorganisir per domain):
- `app/Services/Kependudukan/ImportConflictService.php`
- `app/Services/Kependudukan/ImportService.php`
- `app/Services/Kependudukan/KartuKeluargaService.php`
- `app/Services/Kependudukan/MutasiService.php`
- `app/Services/Kependudukan/PendudukDomisiliService.php`
- `app/Services/Kependudukan/PendudukService.php`
- `app/Services/Kependudukan/VillageStatisticsService.php`
- `app/Services/Keuangan/AnggaranService.php`
- `app/Services/Pelayanan/BantuanSosialService.php`
- `app/Services/Pelayanan/SuratPengajuanService.php`
- `app/Services/Pelayanan/SuratService.php`
- `app/Services/System/BackupService.php`
- `app/Services/System/FileUploadService.php`
- `app/Services/Wilayah/WilayahService.php`

#### Requests — Migrasi ke Domain Subfolder

**Dihapus** (file flat lama di root Requests/):
- `app/Http/Requests/ProfileUpdateRequest.php`
- `app/Http/Requests/StorePendudukDomisiliRequest.php`
- `app/Http/Requests/StorePendudukRequest.php`
- `app/Http/Requests/UpdatePendudukRequest.php`

**Ditambah** (terorganisir per domain):
- `app/Http/Requests/Kependudukan/StorePendudukDomisiliRequest.php`
- `app/Http/Requests/Kependudukan/StorePendudukRequest.php`
- `app/Http/Requests/Kependudukan/UpdatePendudukRequest.php`
- `app/Http/Requests/Keuangan/StoreAnggaranTahunanRequest.php`
- `app/Http/Requests/Keuangan/StorePengeluaranRequest.php`
- `app/Http/Requests/Keuangan/StoreProyekRequest.php`
- `app/Http/Requests/Keuangan/UpdateApbdesRequest.php`
- `app/Http/Requests/Keuangan/UpdatePengeluaranRequest.php`
- `app/Http/Requests/Konten/StoreBeritaRequest.php`
- `app/Http/Requests/Konten/StoreFasilitasDesaRequest.php`
- `app/Http/Requests/Konten/StoreStrukturDesaRequest.php`
- `app/Http/Requests/Konten/StoreUmkmRequest.php`
- `app/Http/Requests/Konten/UpdateBeritaRequest.php`
- `app/Http/Requests/Konten/UpdateFasilitasDesaRequest.php`
- `app/Http/Requests/Konten/UpdateStrukturDesaRequest.php`
- `app/Http/Requests/Konten/UpdateUmkmRequest.php`
- `app/Http/Requests/Profile/ProfileUpdateRequest.php`
- `app/Http/Requests/BantuanSosial/StoreBantuanSosialRequest.php`
- `app/Http/Requests/BantuanSosial/StorePenerimaRequest.php`
- `app/Http/Requests/BantuanSosial/UpdateBantuanSosialRequest.php`
- `app/Http/Requests/BantuanSosial/UpdatePenerimaRequest.php`
- `app/Http/Requests/Mutasi/StoreMutasiRequest.php`
- `app/Http/Requests/Pengaduan/StorePengaduanRequest.php`
- `app/Http/Requests/Pengaduan/UpdatePengaduanRequest.php`

#### Controllers — Slim down (injeksi service)

- `app/Http/Controllers/Tenant/Kependudukan/WilayahController.php` — refactor besar (-300+ baris), delegasi ke `WilayahService`
- `app/Http/Controllers/Tenant/Keuangan/AnggaranController.php` — delegasi ke `AnggaranService`
- `app/Http/Controllers/Tenant/Konten/BeritaController.php` — slim down
- `app/Http/Controllers/Tenant/Konten/FasilitasDesaController.php` — slim down
- `app/Http/Controllers/Tenant/Konten/StrukturDesaController.php` — slim down
- `app/Http/Controllers/Tenant/Konten/UmkmController.php` — slim down
- `app/Http/Controllers/Tenant/Pelayanan/BantuanSosialController.php` — delegasi ke `BantuanSosialService`
- `app/Http/Controllers/Tenant/Pelayanan/SuratPengajuanController.php` — delegasi ke `SuratPengajuanService`
- `app/Http/Controllers/ApiAdminPanel/PendudukController.php` — update namespace request
- Semua controller lain — update `use` statement untuk namespace request/service baru

#### Routes

- `routes/web.php` — update namespace controller, hapus duplikat, rapihkan grouping (+65/-65 net)
- `routes/tenant/admin.php` — hapus 4 route yang sudah dikonsolidasi
- `routes/tenant/pelayanan.php` — update ke namespace controller baru
- `routes/api.php` — update namespace ApiAdminPanel controller

#### Actions & Models

- `app/Actions/Surat/StoreSuratAction.php` — update namespace service
- `app/Models/SuratPengajuan.php` — perbaikan minor
- `app/Observers/MutasiObserver.php` — update service namespace
- `app/Observers/PendudukObserver.php` — update service namespace
- `app/Observers/VillageDataObserver.php` — update service namespace
- `app/Imports/PendudukImport.php` — update service namespace

---

### 🎨 Refactor Frontend — Shared Components (Phase 1)

Membuat library komponen shared untuk mengeliminasi duplikasi kode UI yang terjadi
di 91+ halaman React/Inertia. Estimasi penghematan: **~5,000-8,000 baris kode** setelah Phase 2.

#### Komponen baru di `resources/js/Components/Shared/`

| File | Deskripsi |
|---|---|
| `PageHeader.jsx` | Header gradient hijau reusable — props: `icon`, `title`, `subtitle`, `actions[]`, `backHref`, `titleSize` |
| `TableCard.jsx` | Wrapper tabel dengan header + pagination footer — props: `icon`, `title`, `total`, `pagination`, `noPadding` |
| `EmptyState.jsx` | Empty state dengan Lottie animation + CTA — props: `title`, `message`, `action`, `size` |
| `ActionButtons.jsx` | Tombol View/Edit/Delete per baris tabel — props: `viewHref`, `editHref`, `onDelete`, `extras[]` |
| `Badge.jsx` | Badge status/jenis generik — 12 warna, props: `color`, `icon`, `pulse`, `size`, `dot` |
| `StatCard.jsx` | Kartu statistik — props: `icon`, `label`, `value`, `color`, `trend`, `badge`, `sub` |
| `InfoRow.jsx` | Baris info display-only untuk Show pages — props: `label`, `value`, `icon`, `color` |
| `FormCard.jsx` | Card wrapper section form — props: `icon`, `title`, `actions`, `bodyClass` |
| `FormField.jsx` | Label + input + error — sub-components: `.Input`, `.Select`, `.Textarea` |
| `index.js` | Barrel export semua shared components |

#### Hook baru di `resources/js/lib/`

| File | Deskripsi |
|---|---|
| `useSwalDelete.js` | Standardisasi SweetAlert delete confirmation — menggantikan ~20 baris di tiap Index page |

---

## [v1.6.1-beta] — sebelumnya

- feat(settings): migrate settings and user profile to Inertia React
- feat(DataManagement): migrate Backup, Import, Export, dan Trash modules ke React Inertia

---

## [v1.6.1] — release sebelumnya

- Versi stabil terakhir sebelum refactor besar

---

*Untuk detail lebih lanjut lihat `memory/refactor-progress.md`*

# Changelog

## [Unreleased]

### Backend (sistem-desa-cibatu)
- **Feat**: Implementasi GeoJSON API yang aman (`/api/v1/geojson`) via `DesaInfoApiController`
- **Feat**: Menambahkan label 'Kantor Desa' pada marker peta di `FasilitasDesaForm` dan mengembalikan nilai default marker berdasarkan profil `DesaSetting`
- **Feat**: Update `MapPicker` component agar bisa mengambil GeoJSON langsung lewat prop (meningkatkan kompatibilitas server-fetch API)
- **Feat**: Penambahan label `Latitude (Kantor Desa)` dan `Longitude (Kantor Desa)` di UI Profil Desa untuk memperjelas referensi titik peta
- **Fix**: Mengatasi bug `mkdir` izin folder private untuk surat pengajuan (`Storage::makeDirectory`)
- **Fix**: Perbaikan manajemen upload GeoJSON dan Logo, menyertakan otomatisasi penghapusan file usang (`DesaSettingsController`)
- **Fix**: Penambahan dan perbaikan `Cache::forget` ketika pengaturan batas wilayah (GeoJSON) diperbarui

### Frontend (cibatu-vibe-ai)
- **Feat**: Proxy Next.js (`src/app/api/proxy/[...path]/route.js`) diperbarui dengan layer proteksi cache (stale-while-revalidate 6 jam) khusus untuk data GeoJSON.
- **Feat**: Migrasi `PROXY_KEY` murni ke server-side environment (`.env.local`) tanpa ter-ekspos ke client-side.
- **Feat**: Implementasi rute `clear-cache` baru (`src/app/api/clear-cache`)
- **Refactor**: Optimalisasi pada modul map, berita, kebijakan data, layanan, dan UI navbar agar sinkron dengan update API backend.

---
*Daftar file yang termodifikasi (Git Status):*
- **Backend**: `DesaInfoApiController.php`, `DesaSettingsController.php`, `SuratPengajuanApiController.php`, `HandleInertiaRequests.php`, `DesaSetting.php`, `SuratService.php`, `FasilitasDesaForm.jsx`, `MapPicker.jsx`, `VillageProfile/Index.jsx`, dan file konfigurasi route.
- **Frontend**: `route.js (Proxy)`, `Navbar.jsx`, `layout.jsx`, dan beberapa pages di direktori `src/app`.

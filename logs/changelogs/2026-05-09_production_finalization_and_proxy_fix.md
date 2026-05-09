# Changelog - 9 Mei 2026
## Production Finalization & Proxy Architecture Fix (#JuaraVibeCoding)

### 1. Frontend Optimization (Next.js)
- **Ultra-Robust Copy-to-Clipboard:** Implementasi metode sinkron (textarea fallback) untuk memastikan fitur salin Tracking ID berjalan di semua perangkat mobile (iOS/Android) dan browser non-HTTPS.
- **Visual Feedback:** Menambahkan animasi "NOMOR BERHASIL DISALIN" dan perubahan warna tombol untuk pengalaman pengguna yang lebih baik.
- **Full Hidden Backend Architecture:**
    - Membuat `api/storage` proxy untuk menyembunyikan URL backend asli saat memuat gambar.
    - Sinkronisasi `api/proxy` dengan `ApiProxyController` Laravel (hanya meneruskan `X-Proxy-App-Id`).
    - Fix Next.js 15 `params` Promise bug pada semua endpoint proxy.
- **Image Optimization:** Update `next.config.mjs` untuk whitelist domain produksi API agar `next/image` berfungsi normal.

### 2. Backend Stabilization (Laravel)
- **Data Seeding Fixes:**
    - `BeritaSeeder`: Menambahkan `published_at` agar berita langsung muncul di frontend.
    - `PendudukSeeder`: Memperbaiki panjang NIK/NKK (16 digit) dan implementasi `updateOrCreate` untuk mencegah duplikasi.
    - `SuratTypeSeeder`: Melengkapi data persyaratan dinamis untuk setiap jenis layanan.
- **Middleware & Security:**
    - Update `CspNonceMiddleware` untuk mendukung aset dari `fonts.bunny.net` dan `cdn.tailwindcss.com`.
    - Verifikasi `PrivateApiMiddleware` untuk memastikan keamanan handshake antara Proxy Next.js dan Internal API.
- **Bug Fixes:** Resolusi Error 500 pada Dashboard Wilayah terkait pemetaan variabel `$mapping`.

### 3. Deployment Readiness
- Sinkronisasi environment variabel antara Next.js (`INTERNAL_API_URL`) dan Laravel (`API_KEY`).
- Pembersihan cache sistem (`config`, `view`, `route`) untuk memastikan konfigurasi produksi aktif.

**Status:** 🚀 SIAP DEMO / PRODUCTION READY

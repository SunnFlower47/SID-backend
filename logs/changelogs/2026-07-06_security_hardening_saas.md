# Changelog: Security Hardening SaaS & Monitoring UI Overhaul (2026-07-06)

## Ringkasan Eksekutif
Implementasi penuh 6 item kritis peningkatkan keamanan sistem (*Security Hardening*) pada arsitektur multi-tenant SaaS Desa Cibatu untuk panel Super Admin Landlord (Diskominfo) dan panel Tenant (Desa). Dilengkapi dengan pembaruan antarmuka pemantauan sistem (*Monitoring UI*) yang menyajikan jejak audit keamanan secara terpusat dan terstruktur.

---

## 🛡️ 1. Two-Factor Authentication (2FA / TOTP) Landlord
- **Database**: Penambahan kolom `two_factor_secret`, `two_factor_enabled`, `two_factor_recovery_codes`, dan `two_factor_confirmed_at` pada tabel `central_users`.
- **Backend**: 
  - Implementasi `TwoFactorController` menggunakan package `pragmarx/google2fa` & `chillerlan/php-qrcode` (render QR Code SVG base64).
  - Middleware `RequireLandlord2FA` untuk memblokir akses ke rute `/landlord/*` bagi user yang belum mengaktifkan atau memverifikasi OTP.
  - Alur login pada `LandlordLoginController` diperbarui untuk meminta verifikasi 6 digit kode OTP atau 8 kode pemulihan darurat (*Recovery Codes*).
- **Frontend React**:
  - `TwoFactorSetup.jsx`: Form pemindaian QR Code dan verifikasi aktivasi awal.
  - `TwoFactorVerify.jsx`: Halaman verifikasi login berlapis 2FA dan opsi login dengan kode pemulihan.
  - `Settings/Index.jsx`: Integrasi UI manajemen 2FA (Enable / Disable / View Recovery Codes) yang diproteksi konfirmasi password SweetAlert2.

---

## 🔍 2. Audit Trail Event Login, Logout & Aksi Kritis
- **Backend**:
  - Event listener `LogLandlordAuthEvents` menangkap event `Login`, `Logout`, dan `Failed` dari guard `landlord`.
  - Merekam data aktor (email, ID), alamat IP, User-Agent browser, dan deskripsi kejadian ke tabel central `landlord_audit_logs`.
  - Logging eksplisit untuk aksi administratif sensitif: *Hard Delete* tenant, modifikasi status 2FA, dan penggunaan kode pemulihan darurat.
- **Frontend Monitoring UI**:
  - Pembaruan `MonitoringController` dan `Monitoring/Index.jsx`.
  - Penambahan **Tab Switcher** interaktif bergaya modern untuk menavigasi 4 modul pemantauan:
    1. **Log Audit Keamanan Landlord**: Tabel riwayat insiden keamanan pusat (Login/Logout/Lockout/2FA/Hard Delete).
    2. **Kesehatan & Resource Desa**: Daftar tenant, status database, kuota user, dan kapasitas penyimpanan S3/MinIO.
    3. **Log Aktivitas Tenant Desa**: Riwayat aktivitas operasional dalam desa (*tenant_activity_logs*).
    4. **System Debugger**: Console terminal pembaca file `laravel.log` secara *real-time*.

---

## 🔒 3. Security Headers Hardening & CSP
- **Middleware**: Refactor `CspNonceMiddleware.php`.
- **Perubahan CSP**: Menghapus direktif `'unsafe-inline'` pada `script-src` dan `'unsafe-eval'` yang menurunkan manfaat cryptographic nonce.
- **HTTP Headers Tambahan**:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains` (HSTS)

---

## 🚦 4. Rate Limiting Login Tenant & Landlord
- **Tenant Login**: Menambahkan middleware `throttle:5,1` pada rute `POST /login` di `routes/auth.php` (maksimal 5 percobaan per menit per IP).
- **Landlord Login**: Mempertahankan `throttle:5,1` dan proteksi Google reCAPTCHA v3 pada `routes/landlord.php`.

---

## 🚫 5. Account Lockout Mechanism (Mencegah Brute-Force)
- **Database**: Penambahan kolom `failed_login_attempts` (unsigned integer, default 0) dan `locked_until` (timestamp nullable) pada tabel `central_users`.
- **Backend**:
  - Logika pada `LandlordLoginController`: jika mengalami 5 kali kegagalan otentikasi berturut-turut, akun dikunci secara otomatis selama **15 menit**.
  - Setiap insiden penguncian dicatat ke dalam `landlord_audit_logs` dengan event `account_locked`.

---

## 🔐 6. Enkripsi Field Sensitif `operator_password`
- **Model**: Menambahkan cast Eloquent `'operator_password' => 'encrypted'` pada model `App\Models\Tenant`.
- **Keamanan**: Kredensial kata sandi awal admin desa yang disimpan saat onboarding di tabel `tenants` (kolom JSON `data`) kini terenkripsi dengan algoritma AES-256-CBC menggunakan `APP_KEY`, mencegah kebocoran informasi dari database.

---

## 📊 Status Build & Verifikasi
- **Migrasi**: Berhasil dieksekusi tanpa kendala (`php artisan migrate`).
- **Asset Build**: `npm run build` selesai sukses dalam 1.91s tanpa syntax error.
- **Testing**: Alur 2FA, lockout, audit logging, dan tampilan tab baru pada Monitoring UI telah terverifikasi sinkron dan berjalan dengan mulus.

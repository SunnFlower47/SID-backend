# Changelog: Rebranding Landlord Control Plane ke Admin Panel Central (2026-07-07)

## Ringkasan Eksekutif
Melakukan transisi dan standarisasi penamaan (*rebranding*) dari terminologi **"Diskominfo"** menjadi **"Admin Panel Central" / "Admin Pusat"** secara universal di seluruh ekosistem aplikasi SaaS Desa Cibatu. Perubahan ini dilakukan agar platform bersifat lebih netral, generik, dan siap digunakan pada berbagai skenario deployment berskala VPS / Cloud tanpa keterikatan pada satu instansi spesifik.

---

## 🌐 1. Pembaruan Konfigurasi & Domain Default
- **`config/tenancy.php` & `.env.example`**:
  - Mengubah domain default Landlord dari `diskominfo.sistem-desa-cibatu.test` menjadi `central.sistem-desa-cibatu.test` untuk lingkungan lokal.
  - Mengubah domain produksi dari `diskominfo.purwakarta.desa.id` menjadi `central.purwakarta.desa.id`.
- **`bootstrap/app.php`**:
  - Memperbarui fallback environment `LANDLORD_DOMAIN` ke `central.sistem-desa-cibatu.test` pada routing landlord dan custom guest redirect.

---

## 💾 2. Pembaruan Database Seeder & Migrasi
- **`database/seeders/DatabaseSeeder.php`**:
  - Akun Super Admin Landlord default diperbarui menjadi **Super Admin Central** dengan email **`admin@central.go.id`**.
  - Default central setting untuk `diskominfo_email` diperbarui menjadi `admin@central.go.id`.
- **`database/migrations/2026_06_15_000000_add_sender_name_and_target_tenants_to_broadcast_announcements_table.php`**:
  - Nilai default kolom `sender_name` pada tabel siaran pengumuman diubah dari `Diskominfo` menjadi `Admin Pusat`.

---

## 🖥️ 3. Pembaruan Antarmuka (UI/UX) Landlord & Tenant
- **Navbar & Sidebar Landlord (`LandlordNavbar.jsx`, `LandlordSidebar.jsx`)**:
  - Label identitas aplikasi diperbarui menjadi **Admin Panel Central**.
- **Halaman Autentikasi (`Login.jsx`, `TwoFactorSetup.jsx`, `TwoFactorVerify.jsx`)**:
  - Judul dashboard dan deskripsi keamanan diubah dari "Diskominfo Purwakarta" menjadi **"Admin Panel Central - Pusat Kendali Sistem Multi-Tenant SaaS"**.
  - Label input email login disesuaikan menjadi **Email Admin Pusat**.
- **Halaman Manajemen & Pengaturan (`Users/Index.jsx`, `Settings/Index.jsx`, `Monitoring/Index.jsx`, `Announcements/Index.jsx`)**:
  - Seluruh label halaman, deskripsi hak akses (role permissions), dan nama pengirim default pengumuman diperbarui menjadi **Admin Pusat / Admin Panel Central**.
- **Dashboard Tenant (`Dashboard/Index.jsx`)**:
  - Label badge siaran pengumuman dari pusat diubah menjadi **Admin Pusat Broadcast**.

---

## 📧 4. Pembaruan Email Notification & Backend Services
- **Template Email (`welcome-tenant.blade.php`)**:
  - Surat elektronik sambutan otomatis bagi tenant baru kini mengatasnamakan **Tim Admin Panel Central**.
- **Backend Services & Middlewares (`BsreService.php`, `AppServiceProvider.php`, `NotificationController.php`, dll.)**:
  - Seluruh pesan error, notifikasi blokir tenant nonaktif, pesan validasi kuota user, serta keterangan status sertifikat BSrE disesuaikan untuk mengarahkan pengguna menghubungi **Admin Pusat**.

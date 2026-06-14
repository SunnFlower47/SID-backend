# Changelog: Migrasi Sukses ke Multi-Tenant SaaS (Landlord & Tenant Isolation)

Tanggal: 15 Juni 2026
Versi: 
- Backend: v1.15.0
- Frontend (Next.js): v1.1.0

## Ringkasan Utama

Aplikasi Sistem Desa Purwakarta telah **berhasil dimigrasikan sepenuhnya dari arsitektur Single-Tenant menjadi platform Multi-Tenant SaaS (Software-as-a-Service) yang kokoh**. 

Sistem kini dirancang untuk skala tingkat kabupaten dengan pemisahan mutlak data antar desa, efisiensi resource, dan panel kendali terpusat bagi Diskominfo. Infrastruktur baru ini terbagi menjadi dua konteks utama:
1. **Landlord Context (Diskominfo Control Plane)**: Berperan sebagai pusat manajemen dan pengawasan sistem. Bertanggung jawab atas pendaftaran desa baru (onboarding), alokasi kuota resource, manajemen hak akses staff Diskominfo, pengumuman massal, pemantauan status desa, serta konfigurasi pengaturan global.
2. **Tenant Context (Desa Data Plane)**: Setiap desa berjalan di atas lingkungan logis terisolasi yang memiliki basis data dinamis sendiri, media storage (MinIO/S3) terpisah, berkas log terenkapsulasi, dan sistem otentikasi mandiri.

---

## Detail Arsitektur & Spesifikasi Sistem

### 1. Multi-Tenancy Core & Database Isolation (Multi-Database Approach)
* **Tenancy Engine (Stancl Tenancy)**: Mengintegrasikan tenancy package yang mendeteksi subdomain/domain request secara real-time. Begitu request masuk, engine mencocokkan host URL dengan data domain di database landlord dan secara otomatis beralih (*switches*) ke koneksi database tenant terkait.
* **Isolasi Database Mutlak**: 
  - **Database Central (`db_central`)**: Menyimpan tabel global seperti `tenants`, `domains`, `central_users`, `central_roles`, `central_settings`, dan `announcements`.
  - **Database Tenant (`db_tenant_[tenant_id]`)**: Setiap desa memiliki database fisik tersendiri. Semua operasi baca-tulis terisolasi penuh di level database masing-masing, mencegah kebocoran data (*cross-tenant data leakage*) dan memastikan performa query yang optimal tanpa interferensi antar desa.
* **Isolasi Berkas Log**: Berkas log sistem Laravel dipisahkan per tenant (`storage/logs/tenant_{id}/laravel.log`) untuk mempercepat proses debugging dan menjaga privasi aktivitas admin tiap desa.
* **Isolasi Media & Storage**: Berkas yang diunggah oleh desa disimpan di direktori khusus (`storage/tenant_{id}/` atau bucket path terpisah di S3/MinIO) sehingga berkas antar desa tidak tercampur atau dapat diakses secara ilegal oleh desa lain.
* **Isolasi Sesi & Cookie Browser**: Cookie sesi Laravel disuffixkan dengan ID tenant secara dinamis (`session.cookie_tenant_{id}`). Ini mencegah tabrakan sesi (*session hijacking* atau *overwrite*) saat seorang pengguna membuka panel admin beberapa desa yang berbeda pada peramban web yang sama secara bersamaan.

### 2. Kesiapan Pilot & Manajemen Siklus Hidup Tenant
* **Onboarding & Rollback Otomatis**: Pembuatan desa baru dilengkapi dengan DB Transactions. Jika proses pembuatan database tenant, migrasi schema, atau seeding data awal menemui kegagalan, sistem secara otomatis melakukan rollback penuh pada database landlord untuk mencegah data zombi dan inkonsistensi sistem.
* **Aktivasi & Penonaktifan Lembut (Soft-Deactivation)**:
  - Landlord memiliki kendali penuh untuk menonaktifkan tenant sementara waktu.
  - Jika dinonaktifkan, akses ke Admin Panel Desa (Inertia/React) dan Citizen Portal (Next.js) langsung diblokir secara otomatis di level middleware backend.
* **Artisan Hard-Delete**: Menyediakan perintah CLI `php artisan tenants:hard-delete {id}` untuk menghapus seluruh data tenant secara permanen termasuk database tenant, data domain, dan menghapus direktori media penyimpanan terkait secara bersih.
* **Zona Bahaya UI (Danger Zone)**: Menu edit desa di Landlord Panel dilengkapi Danger Zone khusus. Penghapusan tenant melalui UI dilindungi oleh konfirmasi ganda SweetAlert2 dan verifikasi ketik slug nama desa untuk menghindari kesalahan klik yang fatal.

### 3. Kontrol Resource & Quota Enforcement
* **Batas Kapasitas Penyimpanan (Storage Quota)**:
  - Diskominfo dapat menentukan batas kapasitas penyimpanan (misal: 50MB, 1GB) per desa.
  - Sistem melakukan kalkulasi total ukuran berkas di storage directory tenant secara real-time sebelum menyetujui proses upload berkas baru. Jika kuota penuh, proses unggah diblokir dan error informatif dikembalikan.
* **Batas Pengguna (User Limit)**:
  - Membatasi jumlah pengguna admin/staf yang dapat dibuat oleh masing-masing desa.
  - Validasi diterapkan pada level model event `creating` di Laravel, menolak pembuatan user baru apabila jumlah user aktif telah menyentuh batas kuota paket.
* **Visualisasi Dashboard Quota**: Dashboard admin desa menampilkan progress bar interaktif dengan indikator warna (hijau/kuning/merah) untuk memantau kapasitas penyimpanan dan kuota pengguna yang tersisa.

### 4. Manajemen Peran Dinamis Landlord (Central Roles Settings)
* **Tabel Dinamis `central_roles`**: Menggantikan peran hardcoded di panel landlord. Hak akses admin Diskominfo kini bersifat dinamis dan dapat dikelola langsung dari basis data.
* **Looping Gate & Caching Performa**:
  - Laravel Gates didefinisikan secara dinamis dengan memetakan permissions yang melekat pada masing-masing role.
  - Untuk menghindari beban kueri database pada setiap request, data permissions di-cache secara aman dengan key `central_role_perms_{$role_id}`.
  - Cache invalidation diterapkan secara otomatis menggunakan event model (`saved` & `deleted`) sehingga setiap perubahan hak akses di menu pengaturan langsung aktif seketika tanpa perlu restart server.
* **UI Manajemen Role & Proteksi Superadmin**: Tab khusus di Menu Pengaturan Landlord untuk manajemen CRUD role dan penugasan permissions. Peran `superadmin` dikunci secara sistem dan tidak dapat diubah atau dihapus untuk mencegah *system lock-out*.

### 5. Pengamanan Tambahan & Pencegahan Serangan (Security Hardening)
* **Anti Brute-force & Rate Limiting**: Memasang limitasi akses pada rute login Landlord menggunakan middleware `throttle:5,1` (maksimal 5 kali percobaan login per menit) untuk menangkal serangan kamus dan brute-force.
* **Google reCAPTCHA v3**:
  - Mengintegrasikan API reCAPTCHA v3 pada halaman masuk Landlord.
  - Verifikasi skor reCAPTCHA dilakukan secara asinkron di sisi server melalui middleware kustom. Jika terdeteksi aktivitas bot, request masuk diblokir dengan pesan validasi yang jelas.
* **Normalisasi Domain Case-Insensitive**: Domain/subdomain yang dimasukkan ke URL secara otomatis diubah menjadi huruf kecil (lowercase) sebelum dicocokkan dengan database. Ini menjamin akses aplikasi tetap berjalan lancar walaupun pengguna menuliskan URL dengan variasi huruf besar (misal: `Cibatu.PurwakartaKab.go.id`).
* **Layout Sticky & Responsif**: Memperbaiki antarmuka navigasi sidebar Landlord (`md:sticky md:top-0 md:h-screen`) agar tombol Log Out dan navigasi utama selalu berada dalam viewport pengguna, memberikan kenyamanan operasional yang premium bagi administrator.

### 6. Integrasi Citizen Portal (Next.js - Cibatu Vibe AI)
* **API Tenancy Handshake**: Next.js mendeteksi subdomain pengakses dan meneruskannya ke backend via API Gateway. Middleware backend `InitializeTenancyForApi` memvalidasi status keaktifan tenant.
* **Penanganan Website Nonaktif**:
  - Jika tenant berstatus nonaktif, API backend merespons dengan HTTP Status `403 TENANT_INACTIVE` dan melampirkan informasi kontak darurat Diskominfo (No. Telepon, Email, Alamat) yang diambil langsung dari pengaturan pusat landlord (`central_settings`).
  - Next.js menangkap respons 403 ini pada level interceptor `server-api.js` dan me-render halaman blokir premium bertema "Website Desa Dinonaktifkan" secara dinamis tanpa menampilkan data tiruan (*no fallback placeholders*).
  - Tampilan halaman blokir didesain dengan estetika modern, micro-animations, dan tombol hubungi kami yang terhubung langsung ke kontak resmi Diskominfo Purwakarta.

---

*Status Platform: Migrasi SaaS Sukses, Sistem Stabil, & Siap Digunakan untuk Uji Coba Pilot Project.*


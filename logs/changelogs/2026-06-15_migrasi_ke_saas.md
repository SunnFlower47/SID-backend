# Changelog: Migrasi Sukses ke Multi-Tenant SaaS (Landlord & Tenant Isolation)

Tanggal: 15 Juni 2026

## Ringkasan Utama

Aplikasi Sistem Desa Purwakarta telah **berhasil dimigrasikan sepenuhnya dari arsitektur Single-Tenant menjadi platform Multi-Tenant SaaS (Software-as-a-Service) yang kokoh**. 

Sistem kini terbagi menjadi dua konteks utama:
1. **Landlord Context (Diskominfo)**: Pusat manajemen sistem untuk pendaftaran desa baru, alokasi kuota, pengumuman massal, pemantauan sistem, dan pengaturan sistem.
2. **Tenant Context (Desa)**: Setiap desa memiliki basis data terisolasi secara dinamis, penyimpanan media (S3/MinIO) terpisah, berkas log terisolasi, dan cookie sesi independen.

---

## Rincian Implementasi & Fitur Baru

### 1. Multi-Tenancy Core & database Isolation
* **Tenancy Engine**: Menggunakan Stancl Tenancy untuk inisialisasi basis data secara otomatis saat request subdomain masuk.
* **Separation of Concerns**: Database central (`db_central`) menampung data kontrol inti, sedangkan tiap desa memiliki database sendiri (`db_tenant_[tenant_id]`).
* **Session & Cookie Isolation**: Cookie session di-suffix dengan ID tenant secara dinamis untuk menghindari tabrakan session pada browser/tab yang sama.
* **Log & File Isolation**: Berkas log aplikasi (`laravel.log`) dan storage file (`s3` / `local`) terisolasi penuh per direktori tenant.

### 2. Kesiapan Pilot & Penanganan Tenant
* **Onboarding & Rollback**: Proses migrasi database tenant baru dilengkapi rollback otomatis jika onboarding gagal, mencegah terbentuknya basis data zombi.
* **Aktivasi & Soft-Deactivation**: Tombol nonaktifkan tenant mematikan akses ke Admin Panel Desa (Inertia/Laravel) dan Citizen Portal (Next.js) dengan halaman error visual yang premium dan detail bantuan terintegrasi.
* **Artisan Hard-Delete**: Perintah CLI `tenants:hard-delete {id}` untuk pembersihan data desa secara permanen yang sadar dan aman.
* **Zona Bahaya (Danger Zone)**: UI khusus di edit desa dengan verifikasi ketik slug desa & SweetAlert2 double-confirmation untuk menghapus data.

### 3. Kontrol Resource & Quota Enforcement
* **Batas Pengguna**: Batas maksimum pengguna (User Limit) dipasang di level model dan divalidasi saat pendaftaran pengguna baru.
* **Kapasitas Penyimpanan (Storage Quota)**: Validasi total penggunaan berkas S3/MinIO sebelum proses unggah data baru diselesaikan.
* **Sistem Widget**: Menampilkan visualisasi progress bar penggunaan kuota penyimpanan dan pengguna di dashboard desa.

### 4. Manajemen Peran Dinamis (Central Roles)
* **Tabel `central_roles`**: Migrasi system roles Diskominfo menjadi dinamis di database.
* **Looping Gate & Caching**: Gate-gate Diskominfo didefinisikan secara dinamis menggunakan database dan di-cache (`central_role_perms_{$role}`) demi performa optimal, didukung cache invalidation otomatis.
* **UI Manajemen Role**: Tab baru di Menu Pengaturan untuk CRUD role lengkap dengan edit izin dan proteksi penuh role `superadmin`.

### 5. Pengamanan & Proteksi Tambahan
* **Anti Brute-force**: Mengamankan gerbang login landlord dengan middleware rate limiter `throttle:5,1`.
* **Google reCAPTCHA v3**: Memasang verifikasi keamanan reCAPTCHA v3 di level middleware dan frontend pada halaman login Landlord.
* **Case-Insensitive Domain**: Normalisasi domain ke lowercase untuk mencegah kegagalan lookup subdomain.
* **Sticky Layout**: Dasbor Landlord menggunakan sticky sidebar navigation agar tombol Log Out selalu dapat diakses dengan mudah di viewport.

---

*Status Migrasi: Sukses & Siap untuk Fase Pilot.*

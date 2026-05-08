# 🌐 API Documentation - Web Desa Cibatu (v1)
Dokumentasi lengkap endpoint API v1 untuk integrasi aplikasi **Web Desa (Landing Page)** dan **Admin Panel (Next.js)**.

---

## 🏗️ Base URL & Auth
- **Endpoint:** `https://api-vilage.sunnflower.site/api/v1`
- **Security:** Seluruh endpoint dilindungi middleware `private.api` (Membutuhkan Header `X-API-KEY`) atau `auth:sanctum` untuk area Admin.

---

## 📊 Statistik & Data Desa

| Method | Endpoint | Description |
|---|---|---|
| GET | `/statistics` | Statistik umum (Penduduk, KK, RT, Mutasi, Berita) |
| GET | `/statistics/penduduk` | Detail statistik penduduk berdasarkan gender & usia |
| GET | `/statistics/kk` | Statistik KK per Dusun |
| GET | `/desa-info` | Profil desa, Pejabat (Kades/Sekdes), dan Logo |
| GET | `/rt-rw` | Daftar pengurus RT & RW se-desa |
| GET | `/bumdes` | Informasi unit usaha BUMDes |

---

## 📄 Layanan Mandiri (Surat-menyurat)

### 1. Master Data & Verifikasi
- **GET** `/surat-types`: Daftar jenis surat, persyaratan, dan `form_json`.
- **POST** `/search-penduduk`: Verifikasi NIK + Tanggal Lahir (v3 Captcha).
- **GET** `/captcha`: Generate captcha image/token.

### 2. Pengajuan & History
- **POST** `/surat-pengajuan`: Kirim pengajuan (Multipart/Form-Data).
- **GET** `/surat-pengajuan/status`: Cek status (Param: `nomor_surat`).
- **POST** `/surat-pengajuan/history`: Cek riwayat surat (Body: `nik`, `tanggal_lahir`).

---

## 📰 Content Management (CMS)

### 1. Berita & Artikel
- **GET** `/berita`: Daftar berita (support pagination & limit).
- **GET** `/berita/{slug}`: Detail berita berdasarkan slug.
- **GET** `/berita-featured`: Daftar berita unggulan/pilihan.
- **GET** `/berita-search`: Pencarian berita (Param: `q`).
- **GET** `/berita-combined`: Gabungan berita internal desa & eksternal RSS.

### 2. Agenda & Pengumuman
- **GET** `/agenda-desa`: Kalender kegiatan desa (support filter `bulan`, `tahun`).
- **GET** `/announcements`: Pengumuman resmi dari kantor desa.

### 3. Proyek & Ekonomi
- **GET** `/proyek-desa`: Daftar proyek pembangunan desa per tahun.
- **GET** `/umkm-unggulan`: Produk UMKM pilihan warga.
- **GET** `/transparansi`: Data APBDes dan alokasi dana.

---

## 🗣️ Interaksi Warga

### 1. Pengaduan (Lapor!)
- **GET** `/pengaduan`: List pengaduan publik (yang sudah disetujui).
- **POST** `/pengaduan/submit`: Kirim aduan baru (Title, Desc, Category, Photo).

### 2. Testimoni
- **GET** `/testimoni`: List testimoni warga.
- **POST** `/testimoni`: Kirim testimoni baru (Rating 1-5).

### 3. Kontak
- **POST** `/contact/submit`: Kirim pesan lewat form Hubungi Kami.

---

## 🛠️ Utilitas Sistem
- **GET** `/csrf-token`: Ambil CSRF token untuk form security.
- **GET** `/rate-limit-status`: Cek sisa kuota request API kamu.
- **POST** `/bantuan-sosial/check`: Cek status penerima bansos (Wajib NIK).

---

## 🔑 Header Requirements
```http
Accept: application/json
X-API-KEY: your_secret_key_here
```

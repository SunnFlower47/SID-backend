# 📡 API Documentation - Web Desa Cibatu (v1.9.1)

Dokumentasi lengkap endpoint API v1 untuk integrasi **Web Desa (Landing Page)** dan layanan warga.

> **Last Updated:** 31 Mei 2026 — v1.9.1-beta

---

## 🌐 Base URL & Authentication

| Lingkungan | URL |
|---|---|
| Production | `https://api-vilage.sunnflower.site/api/v1` |
| Development | `http://localhost:8000/api/v1` |

**Security:** Semua endpoint publik dilindungi middleware `private.api`. Setiap request harus menyertakan header:

```http
Accept: application/json
X-API-KEY: your_secret_key_here
```

---

## 📊 Statistik & Profil Desa

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/statistics` | Statistik umum (Penduduk, KK, RT, Mutasi, Berita) |
| `GET` | `/statistics/penduduk` | Detail statistik berdasarkan gender & usia |
| `GET` | `/statistics/kk` | Statistik KK per Dusun |
| `GET` | `/statistics/mutasi` | Data mutasi penduduk |
| `GET` | `/public-statistics` | Statistik ringkasan untuk halaman publik |
| `GET` | `/public-statistics/penduduk` | Statistik penduduk publik |
| `GET` | `/public-statistics/info-desa` | Info desa publik |
| `GET` | `/desa-info` | Profil desa, pejabat (Kades/Sekdes), dan logo |
| `GET` | `/geojson` | Data GeoJSON batas wilayah desa |
| `GET` | `/contact-info` | Informasi kontak kantor desa |
| `GET` | `/rt-rw` | Daftar pengurus RT & RW se-desa |
| `GET` | `/bumdes` | Informasi unit usaha BUMDes |

### Contoh Response `/desa-info`
```json
{
  "success": true,
  "data": {
    "nama_desa": "Desa Cibatu",
    "kecamatan": "Cisaat",
    "kabupaten": "Sukabumi",
    "logo": "https://...",
    "kades": { "nama": "...", "foto": "..." },
    "sekdes": { "nama": "...", "foto": "..." },
    "visi": "...",
    "misi": "..."
  }
}
```

---

## 📄 Layanan Surat Online

### Master Data & Verifikasi

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/surat-types` | Daftar jenis surat aktif beserta persyaratan dan `form_json` |
| `POST` | `/search-penduduk` | Verifikasi NIK + Tanggal Lahir (dilindungi Captcha) |
| `GET` | `/captcha` | Generate CAPTCHA image/token |

#### Body `POST /search-penduduk`
```json
{
  "nik": "3202xxxxxxxxxxxxxx",
  "tanggal_lahir": "1990-01-15",
  "captcha_token": "token_dari_captcha_endpoint"
}
```

### Pengajuan & Riwayat Surat

| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/surat-pengajuan` | Kirim pengajuan surat baru (`multipart/form-data`) |
| `GET` | `/surat-status` | Cek status pengajuan (Query: `?nomor_surat=`) |
| `POST` | `/surat-history` | Cek riwayat surat milik warga |

#### Body `POST /surat-history`
```json
{
  "nik": "3202xxxxxxxxxxxxxx",
  "tanggal_lahir": "1990-01-15"
}
```

#### Contoh Response `/surat-types`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama": "Surat Keterangan Usaha",
      "kode_prefix": "SKU",
      "deskripsi": "...",
      "persyaratan": "...",
      "form_json": [...],
      "is_public": true
    }
  ]
}
```

---

## 📰 Konten Desa (Berita & Agenda)

### Berita & Artikel

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/berita` | Daftar berita (Query: `?limit=`, `?page=`, `?kategori=`) |
| `GET` | `/berita/{slug}` | Detail berita berdasarkan slug |
| `GET` | `/berita-featured` | Daftar berita unggulan/pilihan |
| `GET` | `/berita-latest` | Berita terbaru |
| `GET` | `/berita-search` | Pencarian berita (Query: `?q=kata_kunci`) |
| `GET` | `/berita-categories` | Daftar kategori berita yang tersedia |
| `GET` | `/berita-by-category/{category}` | Berita berdasarkan kategori |
| `GET` | `/berita-combined` | Gabungan berita internal + eksternal (RSS) |
| `GET` | `/berita-eksternal` | Berita dari feed RSS eksternal |
| `GET` | `/announcements` | Daftar pengumuman resmi desa |
| `GET` | `/announcements/{id}` | Detail pengumuman |

### Agenda & Kegiatan

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/agenda-desa` | Kalender kegiatan desa (Query: `?bulan=`, `?tahun=`) |
| `GET` | `/agenda-categories` | Daftar kategori agenda |

---

## 🏗️ Proyek & Transparansi

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/proyek-desa` | Daftar proyek pembangunan desa |
| `GET` | `/proyek-desa/{id}` | Detail proyek berdasarkan ID |
| `GET` | `/proyek-desa/tahun/{year}` | Daftar proyek berdasarkan tahun |
| `GET` | `/transparansi` | Data APBDes + ringkasan anggaran |
| `GET` | `/apbdes` | Detail APBDes per tahun (Query: `?tahun=`) |
| `GET` | `/proyek-pembangunan` | Data proyek untuk halaman transparansi |
| `GET` | `/bantuan-sosial-transparansi` | Data penyaluran bantuan sosial |

#### Contoh Response `/transparansi`
```json
{
  "success": true,
  "data": {
    "tahun": 2026,
    "total_pendapatan": 1500000000,
    "total_belanja": 1350000000,
    "sisa_anggaran": 150000000,
    "apbdes": [...],
    "proyek": [...]
  }
}
```

---

## 🏘️ Fasilitas, UMKM & Struktur Desa

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/fasilitas-desa` | Daftar semua fasilitas desa |
| `GET` | `/fasilitas-desa/jenis/{jenis}` | Fasilitas berdasarkan jenis |
| `GET` | `/umkm` | Daftar UMKM warga (Query: `?limit=`, `?search=`) |
| `GET` | `/umkm/{id}` | Detail UMKM berdasarkan ID |
| `GET` | `/umkm-unggulan` | UMKM unggulan/terpilih |
| `GET` | `/umkm-jenis/{jenis}` | UMKM berdasarkan jenis usaha |
| `GET` | `/umkm-statistics` | Statistik UMKM |
| `GET` | `/struktur-desa` | Struktur organisasi perangkat desa |
| `GET` | `/struktur-desa/category/{category}` | Struktur berdasarkan kategori |
| `GET` | `/perangkat-desa` | Data lengkap perangkat desa |
| `GET` | `/kontak-desa` | Daftar kontak person perangkat desa |

---

## 💬 Interaksi & Layanan Warga

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/testimoni` | Daftar testimoni warga yang sudah disetujui |
| `POST` | `/testimoni` | Kirim testimoni baru (Rating 1-5) |
| `GET` | `/testimoni/stats` | Statistik testimoni (rata-rata rating, total, dll.) |
| `GET` | `/testimoni/categories` | Daftar kategori testimoni |
| `GET` | `/pengaduan` | Daftar pengaduan publik yang sudah selesai |
| `POST` | `/pengaduan/submit` | Kirim pengaduan baru |
| `POST` | `/contact/submit` | Kirim pesan lewat form "Hubungi Kami" |
| `GET` | `/contact/info` | Informasi kontak desa untuk halaman kontak |
| `GET` | `/bantuan-sosial` | Daftar program bantuan sosial aktif |
| `POST` | `/bantuan-sosial/check` | Cek status penerima bantuan (Body: `nik`) |

#### Body `POST /testimoni`
```json
{
  "nama": "Budi Santoso",
  "email": "budi@example.com",
  "telepon": "08123456789",
  "testimoni": "Pelayanan desa sangat baik dan cepat.",
  "rating": 5,
  "kategori": "pelayanan",
  "is_anonymous": false
}
```

#### Body `POST /pengaduan/submit`
```json
{
  "nama_pelapor": "Budi Santoso",
  "nik_pelapor": "3202xxxxxxxxxxxxxx",
  "no_hp": "08123456789",
  "email": "budi@example.com",
  "kategori": "infrastruktur",
  "judul": "Jalan Rusak",
  "deskripsi": "Jalan di RT 01 RW 03 berlubang dan membahayakan.",
  "lokasi": "Jl. Raya Cibatu RT 01 RW 03",
  "foto": "(file)"
}
```

#### Body `POST /contact/submit`
```json
{
  "nama": "Siti Rahayu",
  "email": "siti@example.com",
  "telepon": "08987654321",
  "subjek": "Pertanyaan tentang surat domisili",
  "pesan": "Saya ingin bertanya tentang persyaratan surat domisili."
}
```

---

## 🔧 Utilitas Sistem

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/csrf-token` | Ambil CSRF token untuk keamanan form |
| `GET` | `/rate-limit-status` | Cek sisa kuota request API |
| `POST` | `/chat` | Kirim pesan ke AI Chatbot desa |

---

## ❌ Format Error Response

Semua error response mengikuti format berikut:

```json
{
  "success": false,
  "message": "Pesan error yang menjelaskan masalah",
  "errors": {
    "field_name": ["Pesan validasi error"]
  }
}
```

| HTTP Status | Keterangan |
|---|---|
| `200` | OK - Request berhasil |
| `201` | Created - Data berhasil dibuat |
| `400` | Bad Request - Data tidak valid |
| `401` | Unauthorized - API Key salah/tidak ada |
| `403` | Forbidden - Akses ditolak |
| `404` | Not Found - Data tidak ditemukan |
| `422` | Unprocessable Entity - Validasi gagal |
| `429` | Too Many Requests - Rate limit terlampaui |
| `500` | Internal Server Error |

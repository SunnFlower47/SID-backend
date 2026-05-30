# 📁 Project Structure - Sistem Desa Cibatu

> **Versi:** v1.9.1-beta | **Last Updated:** 31 Mei 2026

Dokumen ini menjelaskan struktur direktori dan organisasi kode proyek **Sistem Informasi Desa Cibatu**.

---

## 🏗️ Backend Structure (Laravel 12)

Sistem menggunakan arsitektur **Controller → Service → Action Pattern** untuk memisahkan logika bisnis.

### **Logic Layer**
```
app/
├── Actions/
│   └── Surat/
│       └── StoreSuratAction.php         # Logika pembuatan surat + sinkronisasi
├── Services/
│   ├── MutasiService.php                # Logika manajemen mutasi penduduk
│   ├── PendudukDomisiliService.php      # Logika manajemen penduduk domisili
│   └── FileUploadService.php            # Upload & replace file/gambar
```

### **Controller Layer**
```
app/Http/Controllers/
├── Api/                                 # Controller untuk API publik (Web Desa)
│   ├── BeritaController.php
│   ├── TestimoniController.php
│   ├── UmkmController.php
│   ├── FasilitasDesaController.php
│   ├── StatisticApiController.php
│   ├── TransparansiController.php
│   ├── SuratPengajuanApiController.php
│   ├── PengaduanController.php
│   ├── ContactController.php
│   └── ...
└── Tenant/                              # Controller untuk dashboard admin
    ├── Kependudukan/                    # Warga, KK, Mutasi, Domisili
    ├── Konten/                          # Berita, UMKM, Fasilitas, Testimoni
    ├── Pelayanan/                       # Surat, Pengaduan, Kontak
    ├── Keuangan/                        # APBDes, Proyek, Anggaran
    ├── Aset/                            # Inventaris aset desa
    └── Admin/                           # Pengaturan & profil desa
```

### **Model & Database**
```
app/Models/
├── Penduduk.php        # Core penduduk (SoftDelete)
├── KartuKeluarga.php   # Summary KK (synced by Observer)
├── Mutasi.php          # Log mutasi penduduk
├── SuratPengajuan.php  # Pengajuan surat warga
├── SuratType.php       # Master jenis surat
├── Berita.php          # Berita desa
├── Testimoni.php       # Testimoni warga (tidak bisa diedit)
├── Pengaduan.php       # Pengaduan warga
├── Umkm.php            # Data UMKM
├── FasilitasDesa.php   # Fasilitas desa
├── ProyekDesa.php      # Proyek pembangunan
├── Apbdes.php          # Data APBDes
├── AsetInventaris.php  # Inventaris aset
├── PeraturanDesa.php   # Peraturan/regulasi desa
└── ...
```

### **Observers**
```
app/Observers/
├── MutasiObserver.php      # Auto-sync KartuKeluarga setelah mutasi
└── PendudukObserver.php    # Auto-update statistik setelah perubahan data
```

### **Traits**
```
app/Traits/
├── HasWilayahLabels.php    # Accessor rt_label, rw_label, dusun_label
└── WilayahResolver.php     # Resolver data wilayah (Dusun/RW/RT)
```

---

## ⚛️ Frontend Structure (React 18 + Inertia.js)

Proyek sepenuhnya menggunakan **React + Inertia.js** (sudah tidak ada Blade views aktif untuk admin panel).

```
resources/js/
├── Pages/Tenant/            # Halaman-halaman admin panel
│   ├── Dashboard/
│   ├── Kependudukan/        # Penduduk, KK, Mutasi, Domisili
│   ├── Konten/              # Berita, UMKM, Fasilitas, Struktur Desa
│   ├── Testimoni/           # Daftar & detail testimoni warga
│   ├── Pelayanan/           # Surat, Pengaduan, Kontak, Jenis Surat
│   ├── Keuangan/            # APBDes, Anggaran, Proyek
│   ├── Aset/                # Manajemen inventaris aset
│   └── Admin/               # Pengaturan desa & profil
├── Components/
│   ├── Shared/              # Komponen reusable (PageHeader, TableCard, Badge, dll.)
│   ├── Umkm/                # Komponen spesifik UMKM (form dengan peta)
│   ├── Testimoni/           # Komponen statistik testimoni
│   └── ...
├── Layouts/
│   └── AuthenticatedLayout.jsx   # Layout utama dashboard
└── lib/
    └── utils.js             # Utility functions (cn, dll.)
```

---

## 🛣️ Routes

```
routes/
├── web.php                  # Entry point + auth routes
├── api.php                  # API publik (v1 - Web Desa)
└── tenant/
    ├── kependudukan.php     # Routes penduduk & KK
    ├── pelayanan.php        # Routes surat, testimoni, pengaduan, kontak
    ├── konten.php           # Routes berita, UMKM, fasilitas
    ├── keuangan.php         # Routes APBDes, anggaran, proyek
    └── admin.php            # Routes pengaturan admin
```

---

## 💾 Storage

```
storage/app/
├── templates/surat/         # Template .docx untuk generate surat
└── public/                  # File yang bisa diakses publik (symlink)

public/storage/
├── berita/                  # Gambar berita
├── umkm/                    # Foto UMKM (termasuk koordinat peta)
├── fasilitas-desa/          # Foto fasilitas
├── struktur-desa/           # Foto perangkat desa
├── kontak-desa/             # Foto kontak person
└── ...
```

---

## 📚 Dokumentasi

| File | Deskripsi |
|------|-----------|
| [README.md](../README.md) | Panduan utama & quick start |
| [DATABASE.md](DATABASE.md) | Skema lengkap database |
| [API_WEB_DESA.md](API_WEB_DESA.md) | Dokumentasi API publik untuk web desa |
| [USER_GUIDE.md](USER_GUIDE.md) | Panduan penggunaan admin panel |
| [USER_MANUAL.md](USER_MANUAL.md) | Manual lengkap untuk operator desa |
| [SECURITY_DOCUMENTATION.md](SECURITY_DOCUMENTATION.md) | Dokumentasi keamanan sistem |
| [PERFORMANCE_OPTIMIZATION.md](PERFORMANCE_OPTIMIZATION.md) | Panduan optimasi performa |
| [TEMPLATE_ENV_HOSTING.md](TEMPLATE_ENV_HOSTING.md) | Template konfigurasi hosting |

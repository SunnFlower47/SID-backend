# 🗄️ DATABASE DOCUMENTATION - SISTEM DESA CIBATU

## 📋 Overview Database

**Sistem Informasi Desa Cibatu** menggunakan MySQL 8.0+ sebagai database utama dengan desain yang optimal untuk performa tinggi dan skalabilitas.

---

## 📊 DAFTAR ISI

- [🏗️ Database Architecture](#️-database-architecture)
- [📋 Table Structure](#-table-structure)
- [🔗 Relationships](#-relationships)
- [📈 Indexes & Performance](#-indexes--performance)
- [🔧 Migrations](#-migrations)
- [🌱 Seeders](#-seeders)
- [📊 Data Types](#-data-types)
- [🔒 Security](#-security)
- [📈 Monitoring](#-monitoring)
- [🛠️ Maintenance](#️-maintenance)

---

## 🏗️ DATABASE ARCHITECTURE

### **Database Schema Overview**
```
desa_cibatu
├── Core Tables
│   ├── penduduks (Core data penduduk dengan soft delete)
│   ├── mutasis (Data mutasi dengan detail_tambahan JSON)
│   ├── surats (Data surat keterangan)
│   └── surat_pengajuans (Pengajuan surat online)
├── Management Tables
│   ├── users (User admin)
│   ├── roles (Role management)
│   ├── permissions (Permission management)
│   └── model_has_roles (Role assignments)
├── Settings Tables
│   ├── desa_settings (Pengaturan desa)
│   ├── struktur_desas (Struktur organisasi)
│   └── kontak_desas (Kontak desa)
├── Content Tables
│   ├── beritas (Berita desa)
│   ├── agenda_desas (Agenda kegiatan)
│   ├── pengaduans (Pengaduan warga)
│   └── bantuan_sosials (Program bantuan)
└── System Tables
    ├── migrations (Migration history)
    ├── password_resets (Password reset tokens)
    ├── failed_jobs (Failed queue jobs)
    └── personal_access_tokens (API tokens)
```

### **Database Configuration**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desa_cibatu
DB_USERNAME=desa_user
DB_PASSWORD=strong_password_here
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

---

## 📋 TABLE STRUCTURE

### **Database Overview**
- **Database Name:** `desa_cibatu_databse`
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Character Set:** utf8mb4

### **Tables List**
| Table Name | Description |
|------------|-------------|
| `penduduks` | Core data penduduk (SoftDelete = mutasi) |
| `mutasis` | Log semua mutasi penduduk |
| `kartu_keluargas` | Ringkasan & cache KK (auto-sync via Observer) |
| `histori_pisah_kk` | Riwayat pemisahan KK |
| `surat_pengajuans` | Pengajuan surat online dari warga |
| `surat_types` | Master jenis surat (template + form builder) |
| `penduduk_domisilis` | Data warga domisili sementara |
| `dusuns`, `rws`, `rts` | Master data wilayah (RT/RW/Dusun) |
| `wilayah_change_logs` | Log perubahan data wilayah |
| `desa_settings` | Pengaturan global desa (key-value) |
| `struktur_desas` | Struktur organisasi perangkat desa |
| `master_jabatans` | Master jabatan perangkat desa |
| `kontak_desas` | Kontak person perangkat desa |
| `beritas` | Artikel berita desa |
| `testimonis` | Testimoni warga (read-only, tidak bisa diedit) |
| `pengaduans` | Pengaduan/laporan warga |
| `contact_messages` | Pesan masuk dari form kontak website |
| `bantuan_sosials` | Program bantuan sosial |
| `penerima_bantuan_sosials` | Penerima program bantuan sosial |
| `apbdes` | Data APBDes per tahun anggaran |
| `proyek_desas` | Proyek pembangunan desa |
| `histori_pengeluarans` | Log pengeluaran realisasi anggaran |
| `peraturan_desas` | Peraturan & regulasi desa |
| `fasilitas_desas` | Fasilitas umum milik desa |
| `umkms` | Data UMKM warga (termasuk koordinat peta) |
| `aset_kategoris` | Kategori aset/inventaris desa |
| `aset_barangs` | Katalog barang/aset |
| `aset_inventaris` | Data inventaris aset desa |
| `aset_mutasis` | Log mutasi/perpindahan aset |
| `users` | Akun pengguna admin |
| `roles`, `permissions` | Tabel RBAC (Spatie Permission) |
| `activity_log` | Log aktivitas semua pengguna |
| `sessions` | Sesi login pengguna |
| `cache`, `cache_locks` | Cache aplikasi |
| `personal_access_tokens` | Token API Sanctum |
| `migrations` | Riwayat migrasi database |


---

## 📊 DETAILED TABLE STRUCTURES

### **1. PENDUDUKS (Core Table)**

#### **Table Definition**
```sql
CREATE TABLE penduduks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nkk VARCHAR(255) NOT NULL COMMENT 'Nomor Kartu Keluarga',
    nik VARCHAR(255) UNIQUE NOT NULL COMMENT 'Nomor Induk Kependudukan (16 digit)',
    nama VARCHAR(255) NOT NULL COMMENT 'Nama lengkap penduduk',
    jenis_kelamin VARCHAR(20) NOT NULL COMMENT 'LAKI-LAKI / PEREMPUAN',
    tempat_lahir VARCHAR(255) NOT NULL COMMENT 'Tempat lahir',
    tanggal_lahir DATE NULL COMMENT 'Tanggal lahir',
    agama VARCHAR(255) NOT NULL COMMENT 'Agama',
    status_perkawinan VARCHAR(255) NULL COMMENT 'Status perkawinan',
    kedudukan_keluarga VARCHAR(255) NULL COMMENT 'Kedudukan dalam keluarga',
    pendidikan VARCHAR(255) NOT NULL COMMENT 'Tingkat pendidikan',
    pekerjaan VARCHAR(255) NOT NULL COMMENT 'Pekerjaan',
    nama_ayah VARCHAR(255) NULL COMMENT 'Nama ayah',
    nama_ibu VARCHAR(255) NULL COMMENT 'Nama ibu',
    alamat TEXT NOT NULL COMMENT 'Alamat lengkap',
    rt VARCHAR(255) NOT NULL COMMENT 'Nomor RT',
    rw VARCHAR(255) NOT NULL COMMENT 'Nomor RW',
    dusun VARCHAR(255) NULL COMMENT 'Nama dusun',
    keterangan TEXT NULL COMMENT 'Keterangan tambahan',
    deleted_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete (Menandakan Mutasi)',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    kartu_keluarga_id BIGINT UNSIGNED NULL COMMENT 'DEPRECATED: Tidak digunakan lagi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> [!IMPORTANT]
> **Struktur Utama**: Sistem ini beralih sepenuhnya ke penggunaan `nkk` (string) untuk mengelola hubungan keluarga. Kolom `kartu_keluarga_id` hanya dipertahankan untuk kompatibilitas database lama tetapi tidak digunakan dalam logika aplikasi.

#### **Indexes**
- `PRIMARY KEY` on `id`
- `UNIQUE KEY` on `nik`
- `INDEX` on `agama`
- `INDEX` on `jenis_kelamin`
- `INDEX` on `kedudukan_keluarga`
- `INDEX` on `nkk`
- `INDEX` on `nkk, kedudukan_keluarga` (compound)
- `INDEX` on `pekerjaan`
- `INDEX` on `pendidikan`
- `INDEX` on `rt`
- `INDEX` on `rw`
- `INDEX` on `tanggal_lahir`

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `nkk` | VARCHAR(255) | Nomor Kartu Keluarga (Kunci Hubungan) | NOT NULL |
| `nik` | VARCHAR(255) | NIK 16 digit | UNIQUE, NOT NULL |
| `nama` | VARCHAR(255) | Nama lengkap penduduk | NOT NULL |
| `jenis_kelamin` | VARCHAR(20) | LAKI-LAKI / PEREMPUAN | NOT NULL |
| `tempat_lahir` | VARCHAR(255) | Tempat lahir | NOT NULL |
| `tanggal_lahir` | DATE | Tanggal lahir | NULLABLE |
| `usia` | INT | Usia (dihitung otomatis) | NULLABLE |
| `agama` | VARCHAR(255) | Agama | NOT NULL |
| `status_perkawinan` | VARCHAR(255) | Status perkawinan | NULLABLE |
| `kedudukan_keluarga` | VARCHAR(255) | Kedudukan dalam keluarga | NULLABLE |
| `pendidikan` | VARCHAR(255) | Tingkat pendidikan | NOT NULL |
| `pekerjaan` | VARCHAR(255) | Pekerjaan | NOT NULL |
| `nama_ayah` | VARCHAR(255) | Nama ayah | NULLABLE |
| `nama_ibu` | VARCHAR(255) | Nama ibu | NULLABLE |
| `alamat` | TEXT | Alamat lengkap | NOT NULL |
| `rt` | VARCHAR(255) | Nomor RT | NOT NULL |
| `rw` | VARCHAR(255) | Nomor RW | NOT NULL |
| `dusun` | VARCHAR(255) | Nama dusun | NULLABLE |
| `keterangan` | TEXT | Keterangan tambahan | NULLABLE |
| `deleted_at` | TIMESTAMP | Menandakan penduduk sudah Mutasi (Soft Delete) | NULLABLE |
| `created_at` | TIMESTAMP | Waktu dibuat | NULLABLE |
| `updated_at` | TIMESTAMP | Waktu diupdate | NULLABLE |
| `kartu_keluarga_id` | BIGINT | **DEPRECATED** (Legacy Field) | NULLABLE |

> [!NOTE]
> **Kolom Status**: Kolom `status` telah dihapus. Status penduduk kini ditentukan oleh `deleted_at`. Jika `NULL`, penduduk aktif. Jika terisi, penduduk tersebut sudah mutasi (mati/pindah).

---

### **2. MUTASIS (Mutation Table)**

#### **Table Definition**
```sql
CREATE TABLE mutasis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    penduduk_id BIGINT UNSIGNED NOT NULL,
    jenis_mutasi ENUM('kelahiran','kematian','pindah_masuk','pindah_keluar','pindah_rt_rw','perubahan_data','pisah_kk') NOT NULL,
    kategori_mutasi VARCHAR(100) NOT NULL,
    asal_tujuan VARCHAR(255) NOT NULL,
    tanggal_mutasi DATE NOT NULL,
    detail_tambahan JSON NULL,
    alasan TEXT NOT NULL,
    dokumen_pendukung VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (penduduk_id) REFERENCES penduduks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `penduduk_id` | BIGINT UNSIGNED | Foreign key to penduduks | NOT NULL, FK |
| `jenis_mutasi` | ENUM | Type of mutation | NOT NULL |
| `kategori_mutasi` | VARCHAR(100) | Mutation category | NOT NULL |
| `asal_tujuan` | VARCHAR(255) | Origin/destination | NOT NULL |
| `tanggal_mutasi` | DATE | Mutation date | NOT NULL |
| `detail_tambahan` | JSON | Additional tracking data | NULLABLE |
| `alasan` | TEXT | Reason for mutation | NOT NULL |
| `dokumen_pendukung` | VARCHAR(255) | Supporting document | NULLABLE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |
| `deleted_at` | TIMESTAMP | Soft delete | NULLABLE |

#### **Indexes**
- `PRIMARY KEY` on `id`
- `INDEX` on `penduduk_id`
- `INDEX` on `jenis_mutasi`
- `INDEX` on `penduduk_id, jenis_mutasi` (compound)
- `INDEX` on `created_at`

---

### **3. SURAT_PENGAJUANS (Letter Applications Table)**

#### **Table Definition**
```sql
CREATE TABLE surat_pengajuans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nik_pengaju VARCHAR(255) NOT NULL,
    nama_pengaju VARCHAR(255) NOT NULL,
    email_pengaju VARCHAR(255) NULL,
    no_hp_pengaju VARCHAR(255) NULL,
    jenis_surat VARCHAR(255) NULL,
    penduduk_id BIGINT UNSIGNED NOT NULL,
    nomor_surat VARCHAR(255) UNIQUE NOT NULL,
    keperluan TEXT NULL,
    tujuan VARCHAR(255) NULL,
    tanggal_surat DATE NOT NULL,
    keterangan_tambahan TEXT NULL,
    data_tambahan JSON NULL,
    status ENUM('pending','approved','rejected','completed') NOT NULL,
    keterangan TEXT NULL,
    keterangan_admin VARCHAR(255) NULL,
    approved_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    admin_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (penduduk_id) REFERENCES penduduks(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `nik_pengaju` | VARCHAR(255) | Applicant NIK | NOT NULL |
| `nama_pengaju` | VARCHAR(255) | Applicant name | NOT NULL |
| `email_pengaju` | VARCHAR(255) | Applicant email | NULLABLE |
| `no_hp_pengaju` | VARCHAR(255) | Applicant phone | NULLABLE |
| `jenis_surat` | VARCHAR(255) | Letter type | NULLABLE |
| `penduduk_id` | BIGINT UNSIGNED | Foreign key to penduduks | NOT NULL, FK |
| `nomor_surat` | VARCHAR(255) | Letter number | UNIQUE, NOT NULL |
| `keperluan` | TEXT | Purpose of letter | NULLABLE |
| `tujuan` | VARCHAR(255) | Letter destination | NULLABLE |
| `tanggal_surat` | DATE | Letter date | NOT NULL |
| `keterangan_tambahan` | TEXT | Additional notes | NULLABLE |
| `data_tambahan` | JSON | Additional data | NULLABLE |
| `status` | ENUM | Application status | NOT NULL |
| `keterangan` | TEXT | Admin notes | NULLABLE |
| `keterangan_admin` | VARCHAR(255) | Admin additional notes | NULLABLE |
| `approved_at` | TIMESTAMP | Approval timestamp | NULLABLE |
| `completed_at` | TIMESTAMP | Completion timestamp | NULLABLE |
| `admin_id` | BIGINT UNSIGNED | Foreign key to users | NULLABLE, FK |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

#### **Indexes**
- `PRIMARY KEY` on `id`
- `UNIQUE KEY` on `nomor_surat`
- `INDEX` on `penduduk_id`
- `INDEX` on `admin_id`

---

### **4. SURATS (Letter Templates Table)**

#### **Table Definition**
```sql
CREATE TABLE surats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jenis_surat VARCHAR(255) NOT NULL,
    nomor_surat VARCHAR(255) NOT NULL,
    tahun INT NOT NULL,
    template_path VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `jenis_surat` | VARCHAR(255) | Letter type | NOT NULL |
| `nomor_surat` | VARCHAR(255) | Letter number | NOT NULL |
| `tahun` | INT | Year | NOT NULL |
| `template_path` | VARCHAR(255) | Template file path | NOT NULL |
| `is_active` | BOOLEAN | Active status | DEFAULT TRUE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **5. DESA_SETTINGS (Village Settings Table)**

#### **Table Definition**
```sql
CREATE TABLE desa_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `key` | VARCHAR(255) | Setting key | UNIQUE, NOT NULL |
| `value` | TEXT | Setting value | NOT NULL |
| `description` | TEXT | Setting description | NULLABLE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **6. STRUKTUR_DESAS (Village Structure Table)**

#### **Table Definition**
```sql
CREATE TABLE struktur_desas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jabatan VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    nip VARCHAR(255) NULL,
    level INT NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (parent_id) REFERENCES struktur_desas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `jabatan` | VARCHAR(255) | Position title | NOT NULL |
| `nama` | VARCHAR(255) | Person name | NOT NULL |
| `nip` | VARCHAR(255) | Employee ID | NULLABLE |
| `level` | INT | Hierarchy level | NOT NULL |
| `parent_id` | BIGINT UNSIGNED | Parent position | NULLABLE, FK |
| `is_active` | BOOLEAN | Active status | DEFAULT TRUE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **7. BERITAS (News Table)**

#### **Table Definition**
```sql
CREATE TABLE beritas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    konten TEXT NOT NULL,
    excerpt TEXT NULL,
    gambar VARCHAR(255) NULL,
    kategori VARCHAR(100) NOT NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `judul` | VARCHAR(255) | News title | NOT NULL |
| `slug` | VARCHAR(255) | URL slug | UNIQUE, NOT NULL |
| `konten` | TEXT | News content | NOT NULL |
| `excerpt` | TEXT | News excerpt | NULLABLE |
| `gambar` | VARCHAR(255) | Featured image | NULLABLE |
| `kategori` | VARCHAR(100) | News category | NOT NULL |
| `status` | ENUM | Publication status | DEFAULT 'draft' |
| `published_at` | TIMESTAMP | Publication date | NULLABLE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **8. PENGAJUANS (Complaints Table)**

#### **Table Definition**
```sql
CREATE TABLE pengaduans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_pelapor VARCHAR(255) NOT NULL,
    nik_pelapor VARCHAR(255) NOT NULL,
    no_hp VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    kategori VARCHAR(100) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    lokasi VARCHAR(255) NULL,
    foto VARCHAR(255) NULL,
    status ENUM('baru','diproses','selesai','ditolak') DEFAULT 'baru',
    prioritas ENUM('rendah','sedang','tinggi','urgent') DEFAULT 'sedang',
    tanggapan_admin TEXT NULL,
    admin_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `nama_pelapor` | VARCHAR(255) | Reporter name | NOT NULL |
| `nik_pelapor` | VARCHAR(255) | Reporter NIK | NOT NULL |
| `no_hp` | VARCHAR(255) | Phone number | NULLABLE |
| `email` | VARCHAR(255) | Email address | NULLABLE |
| `kategori` | VARCHAR(100) | Complaint category | NOT NULL |
| `judul` | VARCHAR(255) | Complaint title | NOT NULL |
| `deskripsi` | TEXT | Complaint description | NOT NULL |
| `lokasi` | VARCHAR(255) | Location | NULLABLE |
| `foto` | VARCHAR(255) | Photo attachment | NULLABLE |
| `status` | ENUM | Complaint status | DEFAULT 'baru' |
| `prioritas` | ENUM | Priority level | DEFAULT 'sedang' |
| `tanggapan_admin` | TEXT | Admin response | NULLABLE |
| `admin_id` | BIGINT UNSIGNED | Admin user ID | NULLABLE, FK |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **9. BANTUAN_SOSIALS (Social Assistance Table)**

#### **Table Definition**
```sql
CREATE TABLE bantuan_sosials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_program VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    jenis_bantuan VARCHAR(100) NOT NULL,
    nilai_bantuan DECIMAL(15,2) NOT NULL,
    periode_mulai DATE NOT NULL,
    periode_selesai DATE NOT NULL,
    kuota INT NOT NULL,
    status ENUM('aktif','nonaktif','selesai') DEFAULT 'aktif',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `nama_program` | VARCHAR(255) | Program name | NOT NULL |
| `deskripsi` | TEXT | Program description | NOT NULL |
| `jenis_bantuan` | VARCHAR(100) | Assistance type | NOT NULL |
| `nilai_bantuan` | DECIMAL(15,2) | Assistance value | NOT NULL |
| `periode_mulai` | DATE | Start period | NOT NULL |
| `periode_selesai` | DATE | End period | NOT NULL |
| `kuota` | INT | Quota | NOT NULL |
| `status` | ENUM | Program status | DEFAULT 'aktif' |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **10. PENERIMA_BANTUAN_SOSIALS (Social Assistance Recipients Table)**

#### **Table Definition**
```sql
CREATE TABLE penerima_bantuan_sosials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bantuan_sosial_id BIGINT UNSIGNED NOT NULL,
    penduduk_id BIGINT UNSIGNED NOT NULL,
    status_penerimaan ENUM('terdaftar','diverifikasi','diterima','ditolak') DEFAULT 'terdaftar',
    nilai_diterima DECIMAL(15,2) NULL,
    tanggal_penerimaan DATE NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (bantuan_sosial_id) REFERENCES bantuan_sosials(id) ON DELETE CASCADE,
    FOREIGN KEY (penduduk_id) REFERENCES penduduks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `bantuan_sosial_id` | BIGINT UNSIGNED | Foreign key to bantuan_sosials | NOT NULL, FK |
| `penduduk_id` | BIGINT UNSIGNED | Foreign key to penduduks | NOT NULL, FK |
| `status_penerimaan` | ENUM | Recipient status | DEFAULT 'terdaftar' |
| `nilai_diterima` | DECIMAL(15,2) | Received amount | NULLABLE |
| `tanggal_penerimaan` | DATE | Receipt date | NULLABLE |
| `keterangan` | TEXT | Additional notes | NULLABLE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **11. PENDUDUK_DOMISILIS (Domicile Management Table)**

#### **Table Definition**
```sql
CREATE TABLE penduduk_domisilis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    tempat_lahir VARCHAR(255) NULL,
    tanggal_lahir DATE NULL,
    jenis_kelamin VARCHAR(20) NULL,
    agama VARCHAR(100) NULL,
    status_perkawinan VARCHAR(100) NULL,
    kewarganegaraan VARCHAR(100) NULL,
    pekerjaan VARCHAR(255) NULL,
    asal_daerah VARCHAR(255) NULL,
    alamat_asal TEXT NULL,
    rt_id BIGINT UNSIGNED NULL,
    rw_id BIGINT UNSIGNED NULL,
    dusun_id BIGINT UNSIGNED NULL,
    alamat_tinggal TEXT NULL,
    keperluan_domisili TEXT NULL,
    tanggal_masuk DATE NULL,
    tanggal_berlaku DATE NULL,
    status ENUM('aktif', 'expired', 'mutasi') DEFAULT 'aktif',
    perpanjangan_ke INT DEFAULT 0,
    nomor_surat VARCHAR(255) NULL,
    surat_pengajuan_id BIGINT UNSIGNED NULL,
    catatan TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (surat_pengajuan_id) REFERENCES surat_pengajuans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `nik` | VARCHAR(255) | Resident NIK | NOT NULL |
| `nama` | VARCHAR(255) | Full name | NOT NULL |
| `surat_pengajuan_id` | BIGINT UNSIGNED | Link to parent letter | FK, CASCADE DELETE |
| `status` | ENUM | Residence status | aktif, expired, mutasi |

---

### **12. CONTACT_MESSAGES (Contact Form Messages Table)**

#### **Table Definition**
```sql
CREATE TABLE contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL COMMENT 'Sender name',
    email VARCHAR(255) NOT NULL COMMENT 'Sender email',
    telepon VARCHAR(255) NOT NULL COMMENT 'Sender phone',
    subjek VARCHAR(255) NOT NULL COMMENT 'Message subject',
    pesan TEXT NOT NULL COMMENT 'Message content',
    status ENUM('unread','read','replied','archived') DEFAULT 'unread' COMMENT 'Message status',
    ip_address VARCHAR(45) NULL COMMENT 'Sender IP address',
    user_agent TEXT NULL COMMENT 'Sender browser info',
    read_at TIMESTAMP NULL COMMENT 'When message was read',
    replied_at TIMESTAMP NULL COMMENT 'When message was replied',
    admin_reply TEXT NULL COMMENT 'Admin reply content',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_status_created (status, created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `nama` | VARCHAR(255) | Sender name | NOT NULL |
| `email` | VARCHAR(255) | Sender email | NOT NULL |
| `telepon` | VARCHAR(255) | Sender phone | NOT NULL |
| `subjek` | VARCHAR(255) | Message subject | NOT NULL |
| `pesan` | TEXT | Message content | NOT NULL |
| `status` | ENUM | Message status | DEFAULT 'unread' |
| `ip_address` | VARCHAR(45) | Sender IP address | NULLABLE |
| `user_agent` | TEXT | Sender browser info | NULLABLE |
| `read_at` | TIMESTAMP | When message was read | NULLABLE |
| `replied_at` | TIMESTAMP | When message was replied | NULLABLE |
| `admin_reply` | TEXT | Admin reply content | NULLABLE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

#### **Indexes**
- `PRIMARY KEY` on `id`
- `INDEX` on `status, created_at` (compound)
- `INDEX` on `email`

#### **Status Values**
- `unread` - New message, not yet read by admin
- `read` - Message has been read by admin
- `replied` - Admin has replied to the message
- `archived` - Message has been archived

---

### **13. USERS (System Users Table)**

#### **Table Definition**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Field Descriptions**
| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | Primary key | AUTO_INCREMENT |
| `name` | VARCHAR(255) | User name | NOT NULL |
| `email` | VARCHAR(255) | Email address | UNIQUE, NOT NULL |
| `email_verified_at` | TIMESTAMP | Email verification | NULLABLE |
| `password` | VARCHAR(255) | Hashed password | NOT NULL |
| `remember_token` | VARCHAR(100) | Remember token | NULLABLE |
| `created_at` | TIMESTAMP | Created timestamp | NULLABLE |
| `updated_at` | TIMESTAMP | Updated timestamp | NULLABLE |

---

### **14. ROLES & PERMISSIONS (User Management Tables)**

#### **Roles Table**
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Permissions Table**
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Model Has Roles Table**
```sql
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Model Has Permissions Table**
```sql
CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (permission_id, model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Role Has Permissions Table**
```sql
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **15. KARTU_KELUARGAS (KK Cache Table)**

#### **Table Definition**
```sql
CREATE TABLE kartu_keluargas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nkk VARCHAR(16) UNIQUE NOT NULL,
    nama_kepala_keluarga VARCHAR(255) NULL,
    nik_kepala_keluarga VARCHAR(16) NULL,
    alamat TEXT NULL,
    rt VARCHAR(3) NULL,
    rw VARCHAR(3) NULL,
    dusun VARCHAR(255) NULL,
    jumlah_anggota INT DEFAULT 0,
    anggota_aktif INT DEFAULT 0,
    anggota_mutasi INT DEFAULT 0,
    anggota_meninggal INT DEFAULT 0,
    anggota_pindah INT DEFAULT 0,
    anggota_pisah_kk INT DEFAULT 0,
    status_kk ENUM('normal', 'bermasalah', 'bermasalah_sementara', 'resolved') DEFAULT 'normal',
    mutasi_penyebab_id BIGINT UNSIGNED NULL,
    kk_sementara_id BIGINT UNSIGNED NULL,
    kk_bermasalah_sejak TIMESTAMP NULL,
    catatan_bermasalah TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (mutasi_penyebab_id) REFERENCES mutasis(id) ON DELETE SET NULL,
    FOREIGN KEY (kk_sementara_id) REFERENCES penduduks(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

> [!TIP]
> **Sinkronisasi**: Tabel ini disinkronkan secara otomatis oleh `MutasiObserver` dan `PendudukObserver`. Jangan melakukan update manual pada kolom jumlah anggota.

---

### **16. WILAYAH MASTER (Dusun, RW, RT)**

#### **dusuns Table**
| Field | Type | Description |
|-------|------|-------------|
| `nama` | VARCHAR(100) | Nama Dusun (Unique) |
| `is_active` | BOOLEAN | Status Aktif |

#### **rws Table**
| Field | Type | Description |
|-------|------|-------------|
| `kode` | VARCHAR(3) | Nomor RW (001, 002, dst) |
| `nama` | VARCHAR(100) | Nama RW (Opsional) |

#### **rts Table**
| Field | Type | Description |
|-------|------|-------------|
| `kode` | VARCHAR(3) | Nomor RT (001, 002, dst) |
| `rw_id` | BIGINT | Foreign key ke rws |
| `dusun_id` | BIGINT | Foreign key ke dusuns |

---

### **17. FINANCIAL MODULE (APBDes & Proyek)**

#### **apbdes Table**
| Field | Type | Description |
|-------|------|-------------|
| `tahun` | YEAR | Tahun Anggaran |
| `jenis` | ENUM | pendapatan, belanja, pembiayaan |
| `nama_rekening` | VARCHAR | Nama mata anggaran |
| `anggaran` | DECIMAL | Nilai Pagu |
| `realisasi` | DECIMAL | Nilai Penyerapan |

#### **proyek_desas Table**
| Field | Type | Description |
|-------|------|-------------|
| `nama_proyek` | VARCHAR | Nama pembangunan |
| `anggaran` | DECIMAL | Nilai Proyek |
| `progress` | INT | Persentase (0-100) |
| `status` | ENUM | perencanaan, pelaksanaan, selesai |

---

### **18. SYSTEM TABLES (Laravel Default Tables)**

#### **Migrations Table**
```sql
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Sessions Table**
```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Password Reset Tokens Table**
```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Cache Tables**
```sql
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Jobs Tables**
```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔗 RELATIONSHIPS OVERVIEW

### **Main Relationships**
- `penduduks` ↔ `mutasis` (One-to-Many): History mutasi tiap warga.
- `penduduks` ↔ `kartu_keluargas` (Many-to-One via `nkk`): Pengelompokan keluarga.
- `kartu_keluargas` ↔ `mutasis` (Many-to-One via `mutasi_penyebab_id`): Tracking penyebab KK bermasalah.
- `apbdes` ↔ `histori_pengeluarans` (One-to-Many): Pelacakan penggunaan anggaran per rekening.
- `rws` ↔ `rts` (One-to-Many): Hierarki wilayah desa.
- `surats` ↔ `surat_pengajuans` (One-to-Many): Template yang digunakan untuk pengajuan.

### **Permission System**
- `users` ↔ `roles` (Many-to-Many via `model_has_roles`)
- `users` ↔ `permissions` (Many-to-Many via `model_has_permissions`)
- `roles` ↔ `permissions` (Many-to-Many via `role_has_permissions`)

---

## 📊 DATABASE CONSTRAINTS

### **Foreign Key Constraints**
- All foreign keys use `ON DELETE CASCADE` for data integrity
- User references use `ON DELETE SET NULL` to preserve data
- Self-referencing foreign keys use `ON DELETE SET NULL`

### **Unique Constraints**
- `penduduks.nik` - Unique NIK
- `surat_pengajuans.nomor_surat` - Unique letter number
- `users.email` - Unique email
- `beritas.slug` - Unique URL slug
- `desa_settings.key` - Unique setting key

### **Indexes**
- Primary keys on all tables
- Foreign key indexes for performance
- Compound indexes for common queries
- Unique indexes for data integrity

---

## 🎯 DATABASE FEATURES

### **Data Types Used**
- `BIGINT UNSIGNED` - Primary keys and foreign keys
- `VARCHAR(255)` - Standard text fields
- `TEXT` - Long text content
- `JSON` - Structured data storage
- `ENUM` - Predefined value sets
- `DECIMAL(15,2)` - Monetary values
- `TIMESTAMP` - Date/time fields
- `BOOLEAN` - True/false values

### **Laravel Features**
- Soft deletes on main tables
- Timestamps on all tables
- Eloquent relationships
- Model factories and seeders
- Migration system
- Query builder optimization

---

*Database structure documentation ini mencakup semua tabel utama dalam sistem desa dengan detail lengkap struktur, constraint, dan relasi antar tabel.*
```

---

## 🔧 MIGRATIONS

### **Migration Structure**
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2025_09_20_135447_create_permission_tables.php
├── 2025_09_20_135451_create_activity_log_table.php
├── 2025_09_20_135455_create_penduduks_table.php
├── 2025_09_20_135458_create_mutasis_table.php
├── 2025_09_21_154259_create_histori_pisah_kk_table.php
├── 2025_09_28_080756_create_desa_settings_table.php
├── 2025_09_29_040000_create_surat_pengajuans_table.php
├── 2025_09_29_040001_create_beritas_table.php
├── 2025_09_30_193336_create_bantuan_sosials_table.php
├── 2025_09_30_201535_create_pengaduans_table.php
├── 2025_10_01_051624_create_fasilitas_desas_table.php
├── 2025_10_01_054501_create_struktur_desas_table.php
├── 2025_10_01_061126_create_apbdes_table.php
├── 2025_10_01_061130_create_proyek_desas_table.php
├── 2025_10_03_222710_create_surats_table.php
├── 2025_10_03_224605_add_missing_columns_to_surat_pengajuans_table.php
└── 2025_10_07_023628_create_contact_messages_table.php
```

### **Sample Migration File**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('nkk'); // No. KK
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('jenis_kelamin', 20);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('agama');
            $table->string('status_perkawinan')->nullable();
            $table->string('kedudukan_keluarga')->nullable();
            $table->string('pendidikan');
            $table->string('pekerjaan');
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->text('alamat');
            $table->string('rt');
            $table->string('rw');
            $table->string('dusun')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 20);
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('nik');
            $table->index('nkk');
            $table->index('jenis_kelamin');
            $table->index('rt');
            $table->index('rw');
            $table->index('dusun');
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penduduks');
    }
};
```

### **Running Migrations**
```bash
# Run all migrations
php artisan migrate

# Run specific migration
php artisan migrate --path=/database/migrations/2024_01_01_000002_create_penduduks_table.php

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drop and recreate)
php artisan migrate:fresh

# Migration with seed
php artisan migrate:fresh --seed
```

---

## 🌱 SEEDERS

### **Seeder Structure**
```
database/seeders/
├── DatabaseSeeder.php (Main seeder)
├── RolesAndPermissionsSeeder.php (Role & permission data)
├── DesaSettingsSeeder.php (Village settings)
├── StrukturDesaSeeder.php (Village structure)
├── PendudukSeeder.php (Sample penduduk data)
├── BantuanSosialSeeder.php (Social assistance data)
├── ProyekDesaSeeder.php (Village projects)
└── ApbdesSeeder.php (Village budget data)
```

### **Sample Seeder File**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DesaSetting;

class DesaSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Desa Info
            ['key' => 'nama_desa', 'value' => 'DESA CIBATU', 'group_name' => 'desa_info'],
            ['key' => 'kecamatan', 'value' => 'CIBATU', 'group_name' => 'desa_info'],
            ['key' => 'kabupaten', 'value' => 'Purwakarta', 'group_name' => 'desa_info'],
            ['key' => 'provinsi', 'value' => 'Jawa Barat', 'group_name' => 'desa_info'],
            ['key' => 'kode_pos', 'value' => '41161', 'group_name' => 'desa_info'],
            ['key' => 'alamat_lengkap', 'value' => 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161', 'group_name' => 'desa_info'],
            ['key' => 'telepon', 'value' => '(0264) 123456', 'group_name' => 'desa_info'],
            ['key' => 'email', 'value' => 'desacibatu.2001@gmail.com', 'group_name' => 'desa_info'],
            ['key' => 'website', 'value' => 'https://desa-cibatu.id', 'group_name' => 'desa_info'],
            ['key' => 'latitude', 'value' => '-6.5001403', 'group_name' => 'desa_info'],
            ['key' => 'longitude', 'value' => '107.5342964', 'group_name' => 'desa_info'],
            
            // Surat Settings
            ['key' => 'format_nomor_surat', 'value' => '{nomor_urut}/SP-{jenis_surat}/{tahun}', 'group_name' => 'surat_settings'],
            ['key' => 'reset_nomor_tahunan', 'value' => '1', 'group_name' => 'surat_settings'],
        ];

        foreach ($settings as $setting) {
            DesaSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
```

### **Running Seeders**
```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=DesaSettingsSeeder

# Run with fresh migration
php artisan migrate:fresh --seed
```

---

## 📊 DATA TYPES

### **MySQL Data Types Used**

#### **String Types**
- `VARCHAR(n)` - Variable length string (nama, alamat)
- `TEXT` - Long text (alamat lengkap, alasan)
- `ENUM` - Predefined values (jenis_kelamin, status)

#### **Numeric Types**
- `BIGINT UNSIGNED` - Large integers (IDs, auto-increment)
- `INT` - Regular integers (urutan, counts)

#### **Date/Time Types**
- `DATE` - Date only (tanggal_lahir, tanggal_mutasi)
- `TIMESTAMP` - Date and time (created_at, updated_at)

#### **JSON Type**
- `JSON` - Structured data (data_tambahan)

### **Data Validation Rules**

#### **NIK Validation**
```php
// NIK must be exactly 16 digits
'nik' => 'required|string|size:16|unique:penduduks,nik'

// Example valid NIK: 3201234567890123
```

#### **NKK Validation**
```php
// NKK must be exactly 16 digits
'nkk' => 'required|string|size:16'

// Example valid NKK: 3201234567890124
```

#### **Date Validation**
```php
// Date must be valid and not in future
'tanggal_lahir' => 'required|date|before:today'
'tanggal_mutasi' => 'required|date|before_or_equal:today'
```

---

## 🔒 SECURITY

### **Database Security Measures**

#### **1. User Access Control**
```sql
-- Create dedicated database user
CREATE USER 'desa_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON desa_cibatu.* TO 'desa_user'@'localhost';

-- Revoke unnecessary privileges
REVOKE CREATE, DROP, ALTER ON desa_cibatu.* FROM 'desa_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;
```

#### **2. Data Encryption**
```php
// Encrypt sensitive data
use Illuminate\Support\Facades\Crypt;

// Encrypt NIK before storing
$encryptedNik = Crypt::encryptString($nik);

// Decrypt NIK when retrieving
$decryptedNik = Crypt::decryptString($encryptedNik);
```

#### **3. SQL Injection Prevention**
```php
// Use Eloquent ORM (recommended)
$penduduks = Penduduk::where('nama', 'like', "%{$search}%")->get();

// Use Query Builder with bindings
$penduduks = DB::table('penduduks')
    ->where('nama', 'like', "?")
    ->setBindings(["%{$search}%"])
    ->get();
```

#### **4. Backup Security**
```bash
# Encrypt database backups
mysqldump -u root -p desa_cibatu | gzip | openssl enc -aes-256-cbc -out backup_$(date +%Y%m%d).sql.gz.enc

# Decrypt backup
openssl enc -aes-256-cbc -d -in backup_20240115.sql.gz.enc | gunzip | mysql -u root -p desa_cibatu
```

---

## 📈 MONITORING

### **Database Performance Monitoring**

#### **1. Slow Query Log**
```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL slow_query_log_file = '/var/log/mysql/slow.log';

-- Check slow queries
SHOW VARIABLES LIKE 'slow_query_log%';
```

#### **2. Query Performance Analysis**
```sql
-- Analyze query performance
EXPLAIN SELECT * FROM penduduks WHERE nik = '3201234567890123';

-- Check index usage
SHOW INDEX FROM penduduks;

-- Monitor table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'desa_cibatu'
ORDER BY (data_length + index_length) DESC;
```

#### **3. Connection Monitoring**
```sql
-- Check active connections
SHOW PROCESSLIST;

-- Check connection limits
SHOW VARIABLES LIKE 'max_connections';

-- Monitor connection usage
SHOW STATUS LIKE 'Threads_connected';
SHOW STATUS LIKE 'Max_used_connections';
```

### **Laravel Database Monitoring**

#### **1. Query Logging**
```php
// Enable query logging
DB::enableQueryLog();

// Your queries here
$penduduks = Penduduk::all();

// Get query log
$queries = DB::getQueryLog();
dd($queries);
```

#### **2. Performance Monitoring**
```php
// Monitor query execution time
$start = microtime(true);
$penduduks = Penduduk::with('mutasis')->get();
$end = microtime(true);
$executionTime = ($end - $start) * 1000; // Convert to milliseconds

echo "Query executed in: {$executionTime}ms";
```

---

## 🛠️ MAINTENANCE

### **Regular Maintenance Tasks**

#### **1. Database Optimization**
```sql
-- Optimize tables
OPTIMIZE TABLE penduduks, mutasis, surats, surat_pengajuans;

-- Analyze tables
ANALYZE TABLE penduduks, mutasis, surats, surat_pengajuans;

-- Check table status
CHECK TABLE penduduks, mutasis, surats, surat_pengajuans;
```

#### **2. Index Maintenance**
```sql
-- Rebuild indexes
ALTER TABLE penduduks ENGINE=InnoDB;

-- Check index fragmentation
SELECT 
    table_name,
    index_name,
    ROUND(stat_value * @@innodb_page_size / 1024 / 1024, 2) AS 'Index Size (MB)'
FROM information_schema.INNODB_SYS_TABLESTATS
WHERE table_name IN ('penduduks', 'mutasis', 'surats');
```

#### **3. Data Cleanup**
```sql
-- Clean up old soft-deleted records (older than 1 year)
DELETE FROM penduduks WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Clean up old failed jobs (older than 7 days)
DELETE FROM failed_jobs WHERE failed_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Clean up old password reset tokens (older than 1 hour)
DELETE FROM password_resets WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

### **Backup and Recovery**

#### **1. Automated Backup Script**
```bash
#!/bin/bash
# backup_database.sh

BACKUP_DIR="/var/backups/desa-cibatu"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="desa_cibatu"
DB_USER="desa_user"
DB_PASS="strong_password_here"

mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 30 days of backups
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

#### **2. Recovery Procedure**
```bash
# Restore from backup
gunzip -c db_backup_20240115_120000.sql.gz | mysql -u root -p desa_cibatu

# Verify restoration
mysql -u root -p -e "SELECT COUNT(*) FROM desa_cibatu.penduduks;"
```

---

## 📊 SAMPLE QUERIES

### **Common Database Queries**

#### **1. Statistics Queries**
```sql
-- Total penduduk aktif
SELECT COUNT(*) as total_penduduk 
FROM penduduks 
WHERE deleted_at IS NULL;

-- Penduduk per jenis kelamin
SELECT 
    jenis_kelamin,
    COUNT(*) as jumlah
FROM penduduks 
WHERE deleted_at IS NULL
GROUP BY jenis_kelamin;

-- Penduduk per RT
SELECT 
    rt,
    COUNT(*) as jumlah
FROM penduduks 
WHERE deleted_at IS NULL AND rt IS NOT NULL
GROUP BY rt
ORDER BY rt;
```

#### **2. Kartu Keluarga Queries**
```sql
-- KK dengan anggota aktif
SELECT 
    nkk,
    COUNT(*) as jumlah_anggota,
    SUM(CASE WHEN deleted_at IS NULL THEN 1 ELSE 0 END) as anggota_aktif
FROM penduduks 
WHERE nkk IS NOT NULL AND nkk != ''
GROUP BY nkk
HAVING anggota_aktif > 0;

-- KK bermasalah (ada anggota yang mutasi)
SELECT 
    p.nkk,
    COUNT(*) as total_anggota,
    SUM(CASE WHEN m.id IS NULL THEN 1 ELSE 0 END) as anggota_aktif,
    SUM(CASE WHEN m.id IS NOT NULL THEN 1 ELSE 0 END) as anggota_mutasi
FROM penduduks p
LEFT JOIN mutasis m ON m.penduduk_id = p.id 
    AND m.jenis_mutasi IN ('kematian', 'pindah_keluar')
WHERE p.nkk IS NOT NULL AND p.nkk != ''
GROUP BY p.nkk
HAVING anggota_aktif > 0 AND anggota_mutasi > 0;
```

#### **3. Surat Queries**
```sql
-- Surat per jenis
SELECT 
    jenis_surat,
    COUNT(*) as jumlah,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft
FROM surats
GROUP BY jenis_surat;

-- Surat per bulan
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as bulan,
    COUNT(*) as jumlah_surat
FROM surats
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY bulan;
```

---

## 🔧 TROUBLESHOOTING

### **Common Database Issues**

#### **1. Connection Issues**
```bash
# Check MySQL service
sudo systemctl status mysql

# Check MySQL logs
sudo tail -f /var/log/mysql/error.log

# Test connection
mysql -u desa_user -p -e "SELECT 1;"
```

#### **2. Performance Issues**
```sql
-- Check slow queries
SHOW PROCESSLIST;

-- Check table locks
SHOW OPEN TABLES WHERE In_use > 0;

-- Check index usage
EXPLAIN SELECT * FROM penduduks WHERE nik = '3201234567890123';
```

#### **3. Data Integrity Issues**
```sql
-- Check for orphaned records
SELECT COUNT(*) FROM mutasis m 
LEFT JOIN penduduks p ON p.id = m.penduduk_id 
WHERE p.id IS NULL;

-- Check for duplicate NIK
SELECT nik, COUNT(*) as count 
FROM penduduks 
GROUP BY nik 
HAVING count > 1;
```

---

## 📚 REFERENCES

### **MySQL Documentation**
- [MySQL 8.0 Reference Manual](https://dev.mysql.com/doc/refman/8.0/en/)
- [MySQL Performance Schema](https://dev.mysql.com/doc/refman/8.0/en/performance-schema.html)
- [MySQL Security](https://dev.mysql.com/doc/refman/8.0/en/security.html)

### **Laravel Database**
- [Laravel Database Documentation](https://laravel.com/docs/database)
- [Laravel Migrations](https://laravel.com/docs/migrations)
- [Laravel Seeders](https://laravel.com/docs/seeding)

### **Best Practices**
- [Database Design Best Practices](https://www.guru99.com/database-design.html)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)
- [Database Security](https://dev.mysql.com/doc/refman/8.0/en/security.html)

---

## 🔄 RECENT UPDATES (October 2025)

### **Database Structure Alignment**
- ✅ **Migration Files Updated:** All migration files now match current database structure
- ✅ **Penduduks Table:** Added missing fields (usia, nama_ayah, nama_ibu, dusun, status, keterangan)
- ✅ **Data Type Consistency:** Fixed VARCHAR lengths and nullable constraints
- ✅ **Import Compatibility:** Excel import now works with current structure
- ✅ **Hosting Ready:** Database structure is fully compatible for hosting migration

### **Key Changes Made**
1. **Penduduks Table Structure:**
   - Added `usia` (INT, nullable) for calculated age
   - Added `nama_ayah` and `nama_ibu` (VARCHAR, nullable)
   - Added `dusun` (VARCHAR, nullable) for village area
   - Added `keterangan` (TEXT, nullable) for additional notes
   - Added `status` (VARCHAR(20), NOT NULL) for resident status
   - Changed `jenis_kelamin` from ENUM to VARCHAR(20)
   - Made `tanggal_lahir` nullable for data flexibility

2. **Migration Cleanup:**
   - Removed duplicate/conflicting migration files
   - Aligned all migrations with actual database structure
   - Ensured proper foreign key relationships

3. **Import/Export Compatibility:**
   - Excel import now handles all current fields
   - Backup system works with current structure
   - Data export includes all necessary fields

### **Hosting Migration Status**
- ✅ **Database Structure:** 100% compatible
- ✅ **Migration Files:** All aligned with current DB
- ✅ **Backup System:** Ready for export
- ✅ **Data Integrity:** Maintained throughout updates

---

*Database documentation ini akan terus diperbarui sesuai dengan perkembangan sistem. Untuk pertanyaan atau saran, silakan hubungi tim development.*

**Database Sistem Desa Cibatu - Optimized for Performance & Security!** 🗄️⚡

---

## 📊 TABEL BARU (Ditambahkan v1.7.0 — v1.9.1)

> Tabel-tabel di bawah ini adalah penambahan setelah dokumentasi awal dibuat. Semua sudah aktif digunakan di sistem.

---

### **SURAT_TYPES (Master Jenis Surat)**

> Primary key menggunakan string slug (`sku`, `ahli-waris`). Kolom ditambahkan bertahap via 5 migration berbeda.

```sql
CREATE TABLE surat_types (
  id              VARCHAR(255) NOT NULL,     -- Slug unik, e.g. 'sku', 'ahli-waris'
  nama            VARCHAR(255) NOT NULL,
  kode            VARCHAR(255) NULL,         -- Kode singkat e.g. 'SKU', 'SKD'
  deskripsi       TEXT         NULL,
  persyaratan     TEXT         NULL,
  has_template    TINYINT(1)   NOT NULL DEFAULT 0,
  template_code   VARCHAR(255) NULL,
  icon            VARCHAR(255) NULL,
  color           VARCHAR(255) NULL,
  is_active       TINYINT(1)   NOT NULL DEFAULT 1,
  is_public       TINYINT(1)   NOT NULL DEFAULT 1,  -- Tampil di portal warga?
  form_json       JSON         NULL,         -- Definisi field form dinamis
  file_template   VARCHAR(255) NULL,         -- Path file .docx template
  created_at      TIMESTAMP    NULL,
  updated_at      TIMESTAMP    NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | VARCHAR(255) | Slug unik sebagai PK (bukan auto-increment) |
| `nama` | VARCHAR(255) | Nama lengkap jenis surat |
| `kode` | VARCHAR(255) | Kode singkat (SKU, SKD, dll) |
| `form_json` | JSON | Definisi field formulir dinamis untuk web |
| `file_template` | VARCHAR(255) | Path file `.docx` untuk generate surat |
| `is_public` | TINYINT | Apakah tampil di portal warga |

---

### **PERATURAN_DESAS (Peraturan Desa)**

> Workflow status mengikuti alur BPD: `draft → diajukan_bpd → dibahas → disetujui/ditolak`.

```sql
CREATE TABLE peraturan_desas (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  jenis_peraturan     ENUM('APBDes','Perubahan APBDes','Lpj APBDes','Lainnya') NOT NULL DEFAULT 'APBDes',
  tahun_anggaran      YEAR NOT NULL,
  judul               VARCHAR(255) NOT NULL,
  nomor_peraturan     VARCHAR(255) NULL,
  tanggal_ditetapkan  DATE         NULL,
  status              ENUM('draft','diajukan_bpd','dibahas','disetujui','ditolak') NOT NULL DEFAULT 'draft',
  keterangan_bpd      TEXT         NULL,
  file_dokumen        VARCHAR(255) NULL,     -- Path file PDF
  created_at          TIMESTAMP    NULL,
  updated_at          TIMESTAMP    NULL,
  PRIMARY KEY (id),
  INDEX idx_peraturan (tahun_anggaran, jenis_peraturan, status)
) ENGINE=InnoDB;
```

---

### **MASTER_JABATANS (Master Jabatan)**

> Referensi nama jabatan perangkat desa. Di-seed otomatis (16 jabatan) saat migration berjalan.

```sql
CREATE TABLE master_jabatans (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama        VARCHAR(255) NOT NULL,
  slug        VARCHAR(255) NOT NULL UNIQUE,  -- e.g. 'kepala_desa', 'ketua_rt'
  is_struktur TINYINT(1)   NOT NULL DEFAULT 1,
  is_kontak   TINYINT(1)   NOT NULL DEFAULT 1,
  urutan      INT          NOT NULL DEFAULT 0,
  created_at  TIMESTAMP    NULL,
  updated_at  TIMESTAMP    NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
-- 16 jabatan seed: kepala_desa, sekretaris, bendahara, kasi_*, kepala_dusun, ketua_rw, ketua_rt, ketua_bumdes, dll.
```

---

### **TESTIMONIS (Testimoni Warga)**

> Testimoni warga. Perlu moderasi admin (`pending → approved/rejected`). **Tidak bisa diedit** setelah dibuat untuk menjaga objektivitas.

```sql
CREATE TABLE testimonis (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama         VARCHAR(255) NOT NULL,
  email        VARCHAR(255) NULL,
  telepon      VARCHAR(255) NULL,
  rt_id        BIGINT UNSIGNED NULL,      -- FK nullable (tidak wajib isi)
  rw_id        BIGINT UNSIGNED NULL,
  dusun_id     BIGINT UNSIGNED NULL,
  testimoni    TEXT        NOT NULL,
  status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  rating       INT         NULL,           -- 1–5 bintang
  kategori     VARCHAR(255) NULL,
  is_anonymous TINYINT(1)  NOT NULL DEFAULT 0,
  ip_address   VARCHAR(255) NULL,
  user_agent   VARCHAR(255) NULL,
  created_at   TIMESTAMP    NULL,
  updated_at   TIMESTAMP    NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (rt_id)    REFERENCES rts(id)    ON DELETE SET NULL,
  FOREIGN KEY (rw_id)    REFERENCES rws(id)    ON DELETE SET NULL,
  FOREIGN KEY (dusun_id) REFERENCES dusuns(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

> [!IMPORTANT]
> Fitur **Edit Testimoni** sudah dihapus permanen (v1.9.1). Admin hanya bisa **Approve / Reject / Hapus** testimoni — tidak bisa mengubah isinya.

---

### **APBDES (Anggaran Pendapatan & Belanja Desa)**

> Versi terbaru menambahkan 3 kolom sesuai **Permendagri No. 20 Tahun 2018**: `bidang`, `sub_bidang`, dan `kegiatan`. Kolom `sumber_dana` diubah dari ENUM menjadi VARCHAR(50) agar fleksibel.

```sql
CREATE TABLE apbdes (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tahun           YEAR NOT NULL,
  bidang          TINYINT NULL,            -- 1=Pemerintahan, 2=Pembangunan, 3=Pembinaan, 4=Pemberdayaan, 5=Bencana
  sub_bidang      VARCHAR(10)  NULL,       -- e.g. '1.1', '2.3'
  kegiatan        VARCHAR(200) NULL,
  jenis           ENUM('pendapatan','belanja','pembiayaan') NOT NULL,
  kode_rekening   VARCHAR(255) NOT NULL,
  nama_rekening   VARCHAR(255) NOT NULL,
  sumber_dana     VARCHAR(50)  NULL,       -- Dana Desa, ADD, BHPR, Hibah, dll
  anggaran        DECIMAL(15,2) NOT NULL,
  realisasi       DECIMAL(15,2) NOT NULL DEFAULT 0,
  sisa_anggaran   DECIMAL(15,2) NOT NULL DEFAULT 0,
  keterangan      TEXT         NULL,
  status          ENUM('draft','disetujui','ditolak') NOT NULL DEFAULT 'draft',
  created_at      TIMESTAMP    NULL,
  updated_at      TIMESTAMP    NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
```

> [!NOTE]
> `sumber_dana` sebelumnya adalah ENUM. Diubah ke VARCHAR(50) via migration `2026_05_18_032917` untuk mengakomodasi sumber dana yang beragam.

---

### **PROYEK_DESAS (Proyek Pembangunan)**

```sql
CREATE TABLE proyek_desas (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_proyek       VARCHAR(255) NOT NULL,
  deskripsi         TEXT         NOT NULL,
  jenis             ENUM('infrastruktur','sosial','ekonomi','lingkungan','lainnya') NOT NULL,
  anggaran          DECIMAL(15,2) NOT NULL,
  realisasi         DECIMAL(15,2) NOT NULL DEFAULT 0,
  tanggal_mulai     DATE         NOT NULL,
  tanggal_selesai   DATE         NULL,
  status            ENUM('perencanaan','pelaksanaan','selesai','tertunda','dibatalkan') NOT NULL DEFAULT 'perencanaan',
  progress          INT          NOT NULL DEFAULT 0,   -- 0–100 persen
  lokasi            VARCHAR(255) NOT NULL,
  penanggung_jawab  VARCHAR(255) NOT NULL,
  kontraktor        VARCHAR(255) NULL,
  dokumentasi       TEXT         NULL,                 -- JSON array path foto
  catatan           TEXT         NULL,
  apbdes_id         BIGINT UNSIGNED NULL,
  created_at        TIMESTAMP    NULL,
  updated_at        TIMESTAMP    NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (apbdes_id) REFERENCES apbdes(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

---

### **HISTORI_PENGELUARANS (Log Realisasi Pengeluaran)**

```sql
CREATE TABLE histori_pengeluarans (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_pengeluaran      VARCHAR(255)    NOT NULL,
  apbdes_id             BIGINT UNSIGNED NOT NULL,
  jumlah                DECIMAL(15,2)   NOT NULL,
  tanggal_pengeluaran   DATE            NOT NULL,
  keterangan            TEXT            NULL,
  dokumen               VARCHAR(255)    NULL,      -- File bukti pengeluaran (opsional)
  no_referensi          VARCHAR(255)    NULL,      -- Nomor kwitansi/referensi
  user_id               BIGINT UNSIGNED NOT NULL,
  created_at            TIMESTAMP       NULL,
  updated_at            TIMESTAMP       NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (apbdes_id) REFERENCES apbdes(id)  ON DELETE CASCADE,
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### **ASET_KATEGORIS (Kategori Aset)**

```sql
CREATE TABLE aset_kategoris (
  id      BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  kode    VARCHAR(10)      NOT NULL UNIQUE,  -- Kode golongan: 2, 3, 4, 5, 6
  nama    VARCHAR(100)     NOT NULL,
  urutan  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
```

---

### **ASET_BARANGS (Katalog Barang Aset)**

```sql
CREATE TABLE aset_barangs (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  aset_kategori_id  BIGINT UNSIGNED NOT NULL,
  kode_barang       VARCHAR(20)  NOT NULL UNIQUE,  -- Format: X.XX.XX.XX
  nama_barang       VARCHAR(200) NOT NULL,
  satuan_default    VARCHAR(30)  NULL,              -- m², unit, Buah, Lusin, dll
  created_at        TIMESTAMP    NULL,
  updated_at        TIMESTAMP    NULL,
  deleted_at        TIMESTAMP    NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (aset_kategori_id) REFERENCES aset_kategoris(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### **ASET_INVENTARIS (Inventaris Aset Desa)**

> [!IMPORTANT]
> Tabel ini mengalami **redesign total** via migration `2026_05_22_120000`. Versi lama menyimpan data per-tahun. Versi baru (saat ini) menyimpan data aset **permanen per unit**. Semua perubahan kuantitas/nilai dipindah ke tabel `aset_mutasi`.

```sql
CREATE TABLE aset_inventaris (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  aset_barang_id        BIGINT UNSIGNED NOT NULL,
  nama_barang_override  VARCHAR(255) NULL,    -- Nama kustom (override master)
  satuan                VARCHAR(255) NOT NULL,
  lokasi                VARCHAR(255) NULL,
  tanggal_perolehan     DATE         NULL,
  asal_usul             ENUM('APBDes','Hibah','Aset Asli Desa','Bantuan Pemerintah','Lainnya') NOT NULL DEFAULT 'APBDes',
  kondisi               ENUM('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  keterangan            TEXT         NULL,
  created_at            TIMESTAMP    NULL,
  updated_at            TIMESTAMP    NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (aset_barang_id) REFERENCES aset_barangs(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### **ASET_MUTASI (Log Mutasi Aset)**

> Tabel baru hasil redesign inventaris. Mencatat setiap transaksi penambahan/pengurangan per semester.

```sql
CREATE TABLE aset_mutasi (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  aset_inventaris_id  BIGINT UNSIGNED NOT NULL,
  tahun               INT         NOT NULL,
  semester            TINYINT     NOT NULL DEFAULT 1,   -- 1 atau 2
  tanggal             DATE        NOT NULL,
  jenis               ENUM('tambah','kurang') NOT NULL,
  kwantitas           DECIMAL(15,2) NOT NULL DEFAULT 0,
  nilai               DECIMAL(20,2) NOT NULL DEFAULT 0,
  keterangan          VARCHAR(255) NULL,
  created_at          TIMESTAMP    NULL,
  updated_at          TIMESTAMP    NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (aset_inventaris_id) REFERENCES aset_inventaris(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### **PENDUDUK_DOMISILIS (Warga Domisili Sementara)**

> Data warga pendatang yang tinggal sementara di desa (bukan penduduk KTP desa). Berbeda dari tabel `penduduks`.

```sql
CREATE TABLE penduduk_domisilis (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nik                   VARCHAR(16)  NOT NULL,
  nama                  VARCHAR(255) NOT NULL,
  tempat_lahir          VARCHAR(255) NULL,
  tanggal_lahir         DATE         NULL,
  jenis_kelamin         VARCHAR(20)  NOT NULL,
  agama                 VARCHAR(255) NULL,
  status_perkawinan     VARCHAR(100) NULL,
  pekerjaan             VARCHAR(255) NULL,
  asal_daerah           VARCHAR(255) NULL,
  alamat_asal           TEXT         NULL,
  rt_id                 BIGINT UNSIGNED NULL,
  rw_id                 BIGINT UNSIGNED NULL,
  dusun_id              BIGINT UNSIGNED NULL,
  alamat_tinggal        TEXT         NULL,
  keperluan_domisili    VARCHAR(255) NULL,
  tanggal_masuk         DATE         NOT NULL,
  tanggal_berlaku       DATE         NOT NULL,
  status                ENUM('aktif','expired','dicabut') NOT NULL DEFAULT 'aktif',
  perpanjangan_ke       INT UNSIGNED NOT NULL DEFAULT 0,
  nomor_surat           VARCHAR(255) NULL,
  surat_pengajuan_id    BIGINT UNSIGNED NULL,
  catatan               TEXT         NULL,
  created_by            BIGINT UNSIGNED NULL,
  deleted_at            TIMESTAMP    NULL,
  created_at            TIMESTAMP    NULL,
  updated_at            TIMESTAMP    NULL,
  PRIMARY KEY (id),
  INDEX idx_nik (nik),
  INDEX idx_status (status),
  INDEX idx_berlaku (tanggal_berlaku),
  FOREIGN KEY (rt_id)      REFERENCES rts(id)             ON DELETE SET NULL,
  FOREIGN KEY (rw_id)      REFERENCES rws(id)             ON DELETE SET NULL,
  FOREIGN KEY (dusun_id)   REFERENCES dusuns(id)          ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id)           ON DELETE SET NULL,
  FOREIGN KEY (surat_pengajuan_id) REFERENCES surat_pengajuans(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

> **📅 Terakhir Diperbarui:** 31 Mei 2026 — v1.9.1-beta

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
| `penduduks` | Core resident data |
| `mutasis` | Population mutations |
| `surat_pengajuans` | Letter applications |
| `surats` | Letter templates |
| `desa_settings` | Village settings |
| `struktur_desas` | Village structure |
| `kontak_desas` | Village contact info |
| `beritas` | News articles |
| `pengaduans` | Citizen complaints |
| `bantuan_sosials` | Social assistance programs |
| `penerima_bantuan_sosials` | Social assistance recipients |
| `apbdes` | Village budget |
| `proyek_desas` | Village projects |
| `fasilitas_desas` | Village facilities |
| `umkms` | UMKM data |
| `histori_pisah_kk` | KK separation history |
| `contact_messages` | Contact form messages |
| `users` | System users |
| `roles` | User roles |
| `permissions` | User permissions |
| `activity_log` | System activity logs |
| `migrations` | Migration records |
| `sessions` | User sessions |
| `password_reset_tokens` | Password reset tokens |
| `cache` | Application cache |
| `cache_locks` | Cache locks |

---

## 📊 DETAILED TABLE STRUCTURES

### **1. PENDUDUKS (Core Table)**

#### **Table Definition**
```sql
CREATE TABLE penduduks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kartu_keluarga_id BIGINT UNSIGNED NULL COMMENT 'Foreign key to kartu_keluargas (legacy)',
    nkk VARCHAR(255) NOT NULL COMMENT 'Nomor Kartu Keluarga',
    nik VARCHAR(255) UNIQUE NOT NULL COMMENT 'Nomor Induk Kependudukan (16 digit)',
    nama VARCHAR(255) NOT NULL COMMENT 'Nama lengkap penduduk',
    jenis_kelamin VARCHAR(10) NOT NULL COMMENT 'Jenis kelamin (L/P)',
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
    deleted_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

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
| `nkk` | VARCHAR(255) | Nomor Kartu Keluarga | NOT NULL |
| `nik` | VARCHAR(255) | NIK 16 digit | UNIQUE, NOT NULL |
| `nama` | VARCHAR(255) | Nama lengkap penduduk | NOT NULL |
| `jenis_kelamin` | VARCHAR(20) | Jenis kelamin (L/P) | NOT NULL |
| `tempat_lahir` | VARCHAR(255) | Tempat lahir | NOT NULL |
| `tanggal_lahir` | DATE | Tanggal lahir | NULLABLE |
| `usia` | INT | Usia (calculated) | NULLABLE |
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
| `status` | VARCHAR(20) | Status penduduk | NOT NULL |
| `created_at` | TIMESTAMP | Waktu dibuat | NULLABLE |
| `updated_at` | TIMESTAMP | Waktu diupdate | NULLABLE |
| `deleted_at` | TIMESTAMP | Soft delete | NULLABLE |

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

### **11. CONTACT_MESSAGES (Contact Form Messages Table)**

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

### **12. USERS (System Users Table)**

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

### **13. ROLES & PERMISSIONS (User Management Tables)**

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

### **14. SYSTEM TABLES (Laravel Default Tables)**

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
- `penduduks` ↔ `mutasis` (One-to-Many)
- `penduduks` ↔ `surat_pengajuans` (One-to-Many)
- `users` ↔ `surat_pengajuans` (One-to-Many, admin_id)
- `users` ↔ `pengaduans` (One-to-Many, admin_id)
- `bantuan_sosials` ↔ `penerima_bantuan_sosials` (One-to-Many)
- `penduduks` ↔ `penerima_bantuan_sosials` (One-to-Many)
- `struktur_desas` ↔ `struktur_desas` (Self-referencing, parent_id)

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
├── 2024_01_15_000000_add_tahun_to_surats_table.php
├── 2025_09_20_135447_create_permission_tables.php
├── 2025_09_20_135451_create_activity_log_table.php
├── 2025_09_20_135452_add_event_column_to_activity_log_table.php
├── 2025_09_20_135453_add_batch_uuid_column_to_activity_log_table.php
├── 2025_09_20_135455_create_penduduks_table.php
├── 2025_09_20_135458_create_mutasis_table.php
├── 2025_09_20_182123_make_status_perkawinan_and_kedudukan_keluarga_nullable.php
├── 2025_09_20_192847_add_dusun_to_penduduks_table.php
├── 2025_09_21_154259_create_histori_pisah_kk_table.php
├── 2025_09_22_055543_fix_mutasi_table.php
├── 2025_09_28_080756_create_desa_settings_table.php
├── 2025_09_29_040000_create_surat_pengajuans_table.php
├── 2025_09_29_040001_create_beritas_table.php
├── 2025_09_30_183036_add_soft_deletes_to_mutasis_table.php
├── 2025_09_30_193336_create_bantuan_sosials_table.php
├── 2025_09_30_193405_create_penerima_bantuan_sosials_table.php
├── 2025_09_30_201535_create_pengaduans_table.php
├── 2025_10_01_051624_create_fasilitas_desas_table.php
├── 2025_10_01_054501_create_struktur_desas_table.php
├── 2025_10_01_055250_create_kontak_desas_table.php
├── 2025_10_01_061126_create_apbdes_table.php
├── 2025_10_01_061130_create_proyek_desas_table.php
├── 2025_10_01_065310_create_umkms_table.php
├── 2025_10_02_090645_update_rt_rw_format_to_three_digits.php
├── 2025_10_02_143806_update_jenis_mutasi_enum_values.php
├── 2025_10_03_163956_add_pisah_kk_to_jenis_mutasi_enum.php
├── 2025_10_03_221157_add_jenis_surat_to_surat_pengajuans_table.php
├── 2025_10_03_221502_add_created_by_to_surat_pengajuans_table.php
├── 2025_10_03_222710_create_surats_table.php
├── 2025_10_03_223704_update_surat_pengajuans_table_for_web_desa.php
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


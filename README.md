# 🏛️ SISTEM DESA CIBATU

> **Platform Digital Terintegrasi untuk Administrasi dan Layanan Desa**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)

## 📋 DAFTAR ISI

- [Overview](#overview)
- [Fitur Utama](#fitur-utama)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Dokumentasi](#dokumentasi)
- [Keamanan](#keamanan)
- [Deployment](#deployment)
- [Support](#support)

---

## 🌟 OVERVIEW

**Sistem Desa Cibatu** adalah platform digital terintegrasi yang dirancang khusus untuk mendukung administrasi dan pelayanan desa di era digital. Sistem ini terdiri dari backend admin (Laravel) dan frontend website (React) yang saling terintegrasi untuk memberikan pengalaman terbaik bagi admin desa dan warga.

### **🎯 Tujuan Sistem:**
- ✅ **Digitalisasi Administrasi** - Mengubah proses manual menjadi digital
- ✅ **Pelayanan Online** - Memungkinkan warga mengakses layanan 24/7
- ✅ **Transparansi Data** - Meningkatkan transparansi informasi desa
- ✅ **Efisiensi Operasional** - Meningkatkan efisiensi administrasi desa
- ✅ **Keterbukaan Informasi** - Memudahkan akses informasi publik



---

## 🚀 FITUR UTAMA

### **👥 Manajemen Penduduk**
- 📊 **Data Kependudukan Lengkap** - NIK, nama, alamat, agama, pendidikan, pekerjaan.
- 🏠 **Kartu Keluarga Terintegrasi** - Manajemen KK berbasis `nkk` (string) dengan fitur deteksi **KK Bermasalah**.
- 📈 **Sistem Mutasi V3.0** - Kelahiran, kematian, pindah masuk/keluar, pisah KK dengan fitur:
  - ✅ **Soft Delete Logic** - Penduduk yang mutasi tetap ada di DB (sejarah) namun tidak muncul di daftar aktif.
  - ✅ **Automatic KK Sync** - Observer otomatis memperbarui ringkasan KK saat ada mutasi.
  - ✅ **Kategori Wilayah** - Klasifikasi otomatis asal/tujuan (Dalam/Luar Kota, Luar Negeri).
  - ✅ **Cancel/Undo** - Fitur pembatalan mutasi dengan rollback data yang presisi.
- 🔍 **Pencarian & Filter** - Pencarian instan berdasarkan NIK/Nama/NKK dengan validasi real-time.
- 📊 **Statistik Real-time** - Dashboard statistik demografi yang akurat dan responsif.

### **📄 Surat Online**
- 📋 **Pengajuan Digital** - Warga ajukan surat via website
- ⚡ **Proses Cepat** - Workflow surat yang efisien
- 📱 **Notifikasi Real-time** - Update status via email/SMS
- 📊 **Tracking Status** - Monitor progress pengajuan surat
- 🎯 **Jenis Surat Lengkap** - SKU, SKTM, Domisili, dll

### **📰 Berita & Informasi**
- 📰 **Berita Desa** - Publikasi kegiatan dan informasi desa
- 🌐 **Berita Eksternal** - Integrasi dengan portal berita
- 🏷️ **Kategori Berita** - Organisasi berita berdasarkan kategori
- 🔍 **Pencarian Berita** - Cari berita berdasarkan kata kunci
- 📱 **Responsive Design** - Akses optimal di semua device

### **💬 Interaksi Warga**
- 💬 **Testimoni Warga** - Feedback dan pengalaman warga
- 📋 **Pengaduan Online** - Layanan pengaduan digital
- 📞 **Kontak Langsung** - Form kontak untuk komunikasi
- ⭐ **Rating System** - Sistem rating untuk layanan
- 📊 **Analytics** - Analisis feedback dan pengaduan

### **📊 Dashboard & Analytics**
- 📈 **Dashboard Real-time** - Statistik terkini sistem
- 📊 **Grafik Interaktif** - Visualisasi data penduduk dan layanan
- 📋 **Laporan Otomatis** - Generate laporan periodik
- 🔔 **Notifikasi Sistem** - Alert untuk admin
- 📱 **Mobile Responsive** - Akses optimal di mobile

---

## 🏗️ ARSITEKTUR SISTEM

### **Backend (Laravel 11)**
```
📁 sistem-desa-cibatu/
├── 📁 app/
│   ├── 📁 Http/Controllers/     # API Controllers
│   ├── 📁 Http/Middleware/      # Security Middleware
│   ├── 📁 Models/               # Eloquent Models
│   └── 📁 Services/             # Business Logic
├── 📁 database/
│   ├── 📁 migrations/           # Database Migrations
│   └── 📁 seeders/             # Database Seeders
├── 📁 routes/
│   ├── 📄 api.php              # API Routes
│   └── 📄 web.php              # Web Routes
└── 📁 resources/views/          # Blade Templates
```

### **Frontend (React 18)**
```
📁 web-desa/
├── 📁 src/
│   ├── 📁 components/          # React Components
│   ├── 📁 pages/               # Page Components
│   ├── 📁 services/            # API Services
│   ├── 📁 hooks/               # Custom Hooks
│   └── 📁 utils/               # Utility Functions
├── 📁 public/                  # Static Assets
└── 📁 build/                   # Production Build
```

### **Database Schema**
```sql
-- Core Tables
📊 penduduks          # Data penduduk (Core) dengan logic Soft Delete
📋 mutasis            # Log mutasi penduduk (Lahir, Mati, Pindah)
🏠 kartu_keluargas    # Tabel summary/cache KK (Sync via Observer)
📄 surat_pengajuans   # Sistem pengajuan surat online
📰 beritas            # Berita dan informasi desa
📰 berita_externals   # Berita eksternal
💬 testimonis         # Testimoni warga
📋 pengaduans         # Pengaduan warga
👥 users              # User admin dengan RBAC
🔐 roles & permissions # Role-based access control
```

---

## ⚙️ INSTALASI

### **Prerequisites**
- **PHP** 8.2 atau lebih tinggi
- **Composer** 2.x
- **Node.js** 18.x atau lebih tinggi
- **MySQL** 8.0 atau lebih tinggi
- **Apache/Nginx** web server

### **1. Clone Repository**
```bash
git clone https://github.com/your-repo/sid-backend.git
cd sid-backend
```

### **2. Backend Setup**
```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create storage link
php artisan storage:link
```

### **3. Frontend Setup**
```bash
# Navigate to frontend directory
cd web-desa

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Build for production
npm run build
```

### **4. Web Server Configuration**
```apache
# Apache .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

---

## 🔧 KONFIGURASI

### **Environment Variables**

#### **Backend (.env)**
```env
APP_NAME="Sistem Desa Cibatu"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admin-dscibatu.sunnflower.site

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=desa_cibatu
DB_USERNAME=your_username
DB_PASSWORD=your_password

# API Security
API_KEY=desa-cibatu-2024-secure-key

# reCAPTCHA
RECAPTCHA_V2_SITE_KEY=your_site_key
RECAPTCHA_V2_SECRET_KEY=your_secret_key
RECAPTCHA_V3_SITE_KEY=your_site_key
RECAPTCHA_V3_SECRET_KEY=your_secret_key
```

#### **Frontend (.env)**
```env
REACT_APP_API_URL=https://admin-dscibatu.sunnflower.site/api/v1
REACT_APP_ENVIRONMENT=production
```

---

## 📚 DOKUMENTASI

### **📖 Dokumentasi Utama**

| **Dokumentasi** | **Deskripsi** | **Link** |
|----------------|---------------|----------|
| 🔒 **Security** | Dokumentasi keamanan sistem | [SECURITY_DOCUMENTATION.md](./SECURITY_DOCUMENTATION.md) |
| 📡 **API** | Dokumentasi API endpoints | [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) |
| 🚀 **Deployment** | Panduan deployment & maintenance | [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) |
| 👥 **User Guide** | Panduan penggunaan sistem | [USER_GUIDE.md](./USER_GUIDE.md) |

---

## 🔒 KEAMANAN

### **🛡️ Security Features**
- 🔑 **API Key Authentication** - Proteksi data sensitif
- ⏱️ **Rate Limiting** - Proteksi dari abuse
- 🌐 **CORS Policy** - Kontrol akses cross-origin
- ✅ **Input Validation** - Validasi semua form
- 🛡️ **XSS Protection** - Proteksi dari XSS attacks
- 🚫 **SQL Injection Protection** - Proteksi database
rechapcha   

> **📖 Detail lengkap:** [SECURITY_DOCUMENTATION.md](./SECURITY_DOCUMENTATION.md)

---

## 🚀 DEPLOYMENT

### **🌐 Production URLs**
- **Frontend:** `https://desacibatu.sunnflower.site`
- **Backend:** `https://admin-dscibatu.sunnflower.site`
- **API:** `https://admin-dscibatu.sunnflower.site/api/v1`

### **📋 Quick Deploy**
```bash
# Backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache

# Frontend
npm run build
# Upload build/ to web server
```

> **📖 Detail lengkap:** [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)

---

## 📞 SUPPORT

### **🆘 Technical Support**
- **Email:** tech-support@desacibatu.id
- **Telepon:** +62-xxx-xxx-xxxx
- **Jam Kerja:** Senin - Jumat, 08:00 - 16:00 WIB

### **🔒 Security Issues**
- **Email:** security@desacibatu.id
- **Response Time:** 4 jam

---

## 📄 LISENSI

Distributed under the MIT License. See `LICENSE` for more information.

---

**📅 Last Updated:** April 25, 2026  
**👤 Maintained By:** Antigravity (AI Assistant) & System Administrator  
**✅ Status:** Production Ready (Optimized)

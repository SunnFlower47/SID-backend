# 📁 PROJECT STRUCTURE DOCUMENTATION

Dokumen ini menjelaskan struktur direktori dan organisasi kode dalam proyek **Sistem Informasi Desa Cibatu**.

---

## 🏗️ Backend Structure (Laravel)

Sistem ini menggunakan arsitektur **Service-Action Pattern** untuk memisahkan logika bisnis dari Controller.

### **1. Logic Layer**
- `app/Actions/`: Berisi class aksi tunggal (Single Action Classes) untuk proses yang kompleks.
    - `Surat/StoreSuratAction.php`: Logika pembuatan surat + sinkronisasi mutasi/domisili.
- `app/Services/`: Berisi logika bisnis yang reusable.
    - `MutasiService.php`: Logika manajemen mutasi penduduk.
    - `PendudukDomisiliService.php`: Logika manajemen penduduk domisili.

### **2. Controller Layer**
- `app/Http/Controllers/Tenant/`: Controller utama untuk dashboard admin.
    - `Kependudukan/`: Manajemen warga, KK, Mutasi, dan Domisili.

### **3. Model & Database**
- `app/Models/`: Eloquent models dengan relasi yang sudah dioptimalkan.
- `database/migrations/`: Riwayat perubahan skema database (terkonsolidasi).

---

## ⚛️ Frontend Structure (React & Blade)

Proyek ini sedang dalam masa transisi dari **Blade** ke **React**.

### **1. React (Vite/Inertia)**
- `resources/js/Pages/`: Komponen halaman React.
    - `Kependudukan/Mutasi/`: Halaman manajemen mutasi.
    - `Kependudukan/PendudukDomisili/`: Halaman manajemen domisili.
- `resources/js/Components/`: Komponen UI yang reusable (Button, Modal, Form inputs).

### **2. Legacy (Blade)**
- `resources/views/tenant/`: Halaman-halaman yang belum dimigrasi ke React.

---

## 📂 Public & Storage
- `public/storage/surat/`: Lokasi penyimpanan file surat yang sudah di-generate (PDF).
- `storage/app/templates/`: Lokasi template `.docx` untuk generator surat.

---

## 📖 Dokumentasi Lainnya
- [DATABASE.md](file:///d:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/docs/DATABASE.md): Dokumentasi skema database.
- [USER_MANUAL.md](file:///d:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/docs/USER_MANUAL.md): Panduan penggunaan aplikasi.
- [opensid_features_audit.md](file:///d:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/docs/opensid_features_audit.md): Referensi fitur OpenSID.

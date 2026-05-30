# 👥 USER GUIDE - SISTEM DESA CIBATU

## 📋 DAFTAR ISI
1. [Overview Sistem](#overview-sistem)
2. [Akses Sistem](#akses-sistem)
3. [Dashboard Admin](#dashboard-admin)
4. [Manajemen Penduduk](#manajemen-penduduk)
5. [Manajemen Surat](#manajemen-surat)
6. [Manajemen Berita](#manajemen-berita)
7. [Manajemen Testimoni](#manajemen-testimoni)
8. [Manajemen Pengaduan](#manajemen-pengaduan)
9. [Website Desa](#website-desa)
10. [Troubleshooting](#troubleshooting)

---

## 🌐 OVERVIEW SISTEM

### **Sistem Desa Cibatu** adalah platform digital yang terdiri dari:

#### **🖥️ Backend Admin (Laravel)**
- **URL:** `https://admin-dscibatu.sunnflower.site`
- **Fungsi:** Manajemen data, administrasi, dan kontrol sistem
- **Akses:** Admin dan perangkat desa

#### **🌐 Frontend Website (React)**
- **URL:** `https://desacibatu.sunnflower.site`
- **Fungsi:** Informasi publik, layanan online, dan interaksi warga
- **Akses:** Publik (semua warga)

### **Fitur Utama:**
- ✅ **Manajemen Penduduk** - Data kependudukan lengkap
- ✅ **Surat Online** - Pengajuan surat secara digital
- ✅ **Berita Desa** - Informasi dan kegiatan desa
- ✅ **Testimoni Warga** - Feedback dan pengalaman warga
- ✅ **Pengaduan Online** - Layanan pengaduan digital
- ✅ **Statistik Real-time** - Data statistik terkini

---

## 🔐 AKSES SISTEM

### **1. Login Admin**

#### **Akses Dashboard Admin:**
1. Buka browser dan kunjungi: `https://admin-dscibatu.sunnflower.site`
2. Masukkan **Username** dan **Password**
3. Selesaikan **reCAPTCHA** (jika diperlukan)
4. Klik **"Masuk"**

#### **Kredensial Default:**
- **Username:** `admin`
- **Password:** `password123` *(harus diubah setelah login pertama)*

### **2. Keamanan Login**

#### **reCAPTCHA Protection:**
- Sistem menggunakan reCAPTCHA v3 untuk melindungi dari bot
- Jika gagal, coba refresh halaman dan login ulang

#### **Session Management:**
- Session akan expired setelah 2 jam tidak aktif
- Login ulang diperlukan jika session expired

---

## 📊 DASHBOARD ADMIN

### **1. Overview Dashboard**

#### **Statistik Utama:**
- 📊 **Total Penduduk** - Jumlah penduduk terdaftar
- 👥 **Jenis Kelamin** - Distribusi laki-laki dan perempuan
- 📄 **Surat Selesai** - Jumlah surat yang telah diproses
- 📝 **Pengaduan** - Status pengaduan warga
- 💬 **Testimoni** - Feedback dari warga

#### **Grafik Real-time:**
- **Chart Penduduk** - Visualisasi data penduduk
- **Chart Surat** - Status pengajuan surat
- **Chart Pengaduan** - Trend pengaduan

### **2. Menu Navigasi**

#### **Menu Utama:**
- 🏠 **Dashboard** - Halaman utama
- 👥 **Penduduk** - Manajemen data penduduk
- 📄 **Surat** - Manajemen surat pengajuan
- 📰 **Berita** - Manajemen berita desa
- 💬 **Testimoni** - Manajemen testimoni warga
- 📋 **Pengaduan** - Manajemen pengaduan
- ⚙️ **Pengaturan** - Konfigurasi sistem

---

## 👥 MANAJEMEN PENDUDUK

### **1. Data Penduduk**

#### **Akses Menu Penduduk:**
1. Login ke dashboard admin
2. Klik menu **"Penduduk"**
3. Pilih **"Data Penduduk"**

#### **Fitur Data Penduduk:**
- ✅ **Tambah Penduduk** - Input data penduduk baru
- ✅ **Edit Data** - Update informasi penduduk
- ✅ **Hapus Data** - Soft delete data penduduk
- ✅ **Pencarian** - Cari berdasarkan nama/NIK
- ✅ **Filter** - Filter berdasarkan RT/RW/Jenis Kelamin
- ✅ **Export** - Export data ke Excel/PDF

### **2. Kartu Keluarga**

#### **Manajemen KK:**
1. Klik menu **"Penduduk"**
2. Pilih **"Kartu Keluarga"**
3. Kelola data keluarga

#### **Fitur Kartu Keluarga:**
- ✅ **Tambah KK** - Input kartu keluarga baru
- ✅ **Anggota Keluarga** - Kelola anggota keluarga
- ✅ **Update KK** - Edit data kartu keluarga
- ✅ **Riwayat KK** - Lihat perubahan KK

### **3. Mutasi Penduduk**

#### **Manajemen Mutasi:**
1. Klik menu **"Penduduk"**
2. Pilih **"Mutasi Penduduk"**
3. Kelola data mutasi

#### **Jenis Mutasi:**
- 📥 **Datang** - Penduduk baru pindah masuk
- 📤 **Pindah** - Penduduk pindah keluar
- 💀 **Meninggal** - Penduduk meninggal dunia

---

## 📄 MANAJEMEN SURAT

### **1. Surat Pengajuan**

#### **Akses Pengajuan Surat:**
1. Klik menu **"Surat"**
2. Pilih **"Pengajuan Surat"**
3. Kelola pengajuan surat

#### **Status Surat:**
- 🟡 **Pending** - Menunggu verifikasi
- 🔵 **Processing** - Sedang diproses
- ✅ **Completed** - Selesai dan siap diambil
- ❌ **Rejected** - Ditolak dengan alasan

#### **Jenis Surat:**
- 📋 **SKU** - Surat Keterangan Usaha
- 🏥 **SKTM Dewasa** - Surat Keterangan Tidak Mampu (Dewasa)
- 👶 **SKTM Anak** - Surat Keterangan Tidak Mampu (Anak)
- 🏠 **Domisili** - Surat Keterangan Domisili
- 👶 **Kelahiran** - Surat Keterangan Kelahiran
- 💀 **Kematian** - Surat Keterangan Kematian
- 🚚 **Pindah** - Surat Keterangan Pindah

### **2. Proses Surat**

#### **Workflow Surat:**
1. **Warga** mengajukan surat via website
2. **Admin** verifikasi data dan dokumen
3. **Admin** proses surat sesuai jenis
4. **Admin** update status menjadi "Completed"
5. **Warga** mengambil surat di kantor desa

#### **Aksi yang Dapat Dilakukan:**
- ✅ **Verifikasi** - Cek kelengkapan dokumen
- ✅ **Proses** - Buat surat sesuai template
- ✅ **Approve** - Setujui dan tandai selesai
- ✅ **Reject** - Tolak dengan alasan
- ✅ **Download** - Download surat yang sudah jadi

---

## 📰 MANAJEMEN BERITA

### **1. Berita Desa**

#### **Akses Berita:**
1. Klik menu **"Berita"**
2. Pilih **"Berita Desa"**
3. Kelola berita desa

#### **Fitur Berita:**
- ✅ **Tambah Berita** - Buat berita baru
- ✅ **Edit Berita** - Update konten berita
- ✅ **Hapus Berita** - Hapus berita
- ✅ **Kategori** - Kelola kategori berita
- ✅ **Gambar** - Upload gambar berita
- ✅ **Publish** - Publikasi berita

### **2. Berita Eksternal**

#### **Manajemen Berita Eksternal:**
1. Klik menu **"Berita"**
2. Pilih **"Berita Eksternal"**
3. Kelola sumber berita eksternal

#### **Fitur Berita Eksternal:**
- ✅ **Tambah Sumber** - Tambah portal berita
- ✅ **Sync Berita** - Sinkronisasi berita otomatis
- ✅ **Filter Sumber** - Filter berdasarkan sumber
- ✅ **Update Manual** - Update berita manual

---

## 💬 MANAJEMEN TESTIMONI

### **1. Testimoni Warga**

#### **Akses Testimoni:**
1. Klik menu **"Testimoni"**
2. Pilih **"Testimoni Warga"**
3. Kelola testimoni

#### **Fitur Testimoni:**
- 👁️ **Lihat Testimoni** - Review semua testimoni
- ✅ **Approve/Reject** - Setujui atau tolak testimoni
- 🔒 **Keaslian Terjamin** - Testimoni tidak dapat diedit untuk menjaga objektivitas
- 🏷️ **Kategori** - Kelola kategori testimoni
- ⭐ **Rating** - Lihat rating testimoni
- 📥 **Export** - Export data testimoni

### **2. Statistik Testimoni**

#### **Dashboard Testimoni:**
- 📊 **Total Testimoni** - Jumlah testimoni
- ⭐ **Rating Rata-rata** - Rating keseluruhan
- 📈 **Trend Testimoni** - Grafik testimoni per bulan
- 🏷️ **Kategori Populer** - Kategori yang paling banyak

---

## 📋 MANAJEMEN PENGADUAN

### **1. Pengaduan Warga**

#### **Akses Pengaduan:**
1. Klik menu **"Pengaduan"**
2. Pilih **"Pengaduan Warga"**
3. Kelola pengaduan

#### **Status Pengaduan:**
- 🟡 **Pending** - Menunggu tindak lanjut
- 🔵 **Processing** - Sedang diproses
- ✅ **Selesai** - Pengaduan telah diselesaikan
- ❌ **Ditolak** - Pengaduan ditolak

#### **Fitur Pengaduan:**
- ✅ **Lihat Detail** - Review pengaduan lengkap
- ✅ **Update Status** - Update status pengaduan
- ✅ **Tambah Catatan** - Tambah catatan internal
- ✅ **Upload Dokumen** - Upload dokumen pendukung
- ✅ **Export** - Export data pengaduan

### **2. Kategori Pengaduan**

#### **Jenis Pengaduan:**
- 🏛️ **Pelayanan** - Keluhan pelayanan desa
- 🛣️ **Infrastruktur** - Masalah jalan/fasilitas
- 💧 **Sumber Daya** - Masalah air/listik
- 🌱 **Lingkungan** - Masalah lingkungan
- 👥 **Sosial** - Masalah sosial kemasyarakatan

---

## 🌐 WEBSITE DESA

### **1. Akses Website**

#### **URL Website:**
- **Website Desa:** `https://desacibatu.sunnflower.site`

#### **Fitur Website:**
- 🏠 **Beranda** - Informasi umum desa
- 📰 **Berita** - Berita dan kegiatan desa
- 👥 **Profil Desa** - Informasi desa dan struktur
- 📞 **Kontak** - Informasi kontak dan lokasi
- 📝 **Layanan** - Form layanan online
- 💬 **Testimoni** - Testimoni warga

### **2. Layanan Online**

#### **Form yang Tersedia:**
- 📝 **Testimoni** - Berikan testimoni tentang desa
- 📞 **Kontak** - Kirim pesan ke desa
- 📋 **Pengaduan** - Laporkan masalah/keluhan
- 📄 **Surat Pengajuan** - Ajukan surat secara online

#### **Cara Menggunakan:**
1. Buka website desa
2. Pilih menu layanan yang diinginkan
3. Isi form dengan data yang benar
4. Submit form
5. Tunggu konfirmasi via email/SMS

---

## 🚨 TROUBLESHOOTING

### **1. Masalah Login**

#### **Tidak Bisa Login:**
- ✅ **Cek Username/Password** - Pastikan benar
- ✅ **Cek reCAPTCHA** - Selesaikan reCAPTCHA
- ✅ **Clear Browser Cache** - Hapus cache browser
- ✅ **Coba Browser Lain** - Test di browser berbeda

#### **Session Expired:**
- ✅ **Login Ulang** - Masukkan kredensial lagi
- ✅ **Cek Internet** - Pastikan koneksi stabil

### **2. Masalah Data**

#### **Data Tidak Muncul:**
- ✅ **Refresh Halaman** - Tekan F5 atau Ctrl+R
- ✅ **Cek Filter** - Pastikan filter tidak terlalu ketat
- ✅ **Cek Pencarian** - Kosongkan field pencarian
- ✅ **Cek Database** - Pastikan data ada di database

#### **Error Saat Simpan:**
- ✅ **Cek Validasi** - Pastikan semua field terisi
- ✅ **Cek Format Data** - Pastikan format sesuai
- ✅ **Cek Koneksi** - Pastikan internet stabil

### **3. Masalah Upload**

#### **File Tidak Bisa Upload:**
- ✅ **Cek Ukuran File** - Pastikan tidak terlalu besar
- ✅ **Cek Format File** - Pastikan format diizinkan
- ✅ **Cek Permission** - Pastikan folder upload writable
- ✅ **Cek Disk Space** - Pastikan server punya space

### **4. Masalah Website**

#### **Website Tidak Bisa Diakses:**
- ✅ **Cek URL** - Pastikan URL benar
- ✅ **Cek Internet** - Pastikan koneksi internet
- ✅ **Cek Server** - Pastikan server online
- ✅ **Cek DNS** - Pastikan DNS resolve benar

#### **Form Tidak Bisa Submit:**
- ✅ **Cek JavaScript** - Pastikan JavaScript enabled
- ✅ **Cek Pop-up Blocker** - Matikan pop-up blocker
- ✅ **Cek Firewall** - Pastikan firewall tidak block
- ✅ **Cek CORS** - Pastikan CORS policy benar

---

## 📞 BANTUAN & DUKUNGAN

### **1. Kontak Support**

#### **Technical Support:**
- **Email:** support@desacibatu.id
- **Telepon:** +62-xxx-xxx-xxxx
- **Jam Kerja:** Senin - Jumat, 08:00 - 16:00

#### **Emergency Support:**
- **Telepon:** +62-xxx-xxx-xxxx
- **WhatsApp:** +62-xxx-xxx-xxxx
- **Jam:** 24/7 untuk masalah kritis

### **2. Dokumentasi**

#### **Dokumentasi Teknis:**
- **API Documentation:** [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
- **Security Guide:** [SECURITY_DOCUMENTATION.md](./SECURITY_DOCUMENTATION.md)
- **Deployment Guide:** [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)

#### **Training & Tutorial:**
- **Video Tutorial:** [Link ke YouTube Channel]
- **User Manual:** [Link ke PDF Manual]
- **FAQ:** [Link ke FAQ Page]

### **3. Feedback & Saran**

#### **Cara Memberikan Feedback:**
1. **Via Email** - Kirim ke feedback@desacibatu.id
2. **Via Website** - Gunakan form kontak di website
3. **Via Testimoni** - Berikan testimoni di website
4. **Via Pengaduan** - Laporkan masalah via form pengaduan

---

## 📋 CHECKLIST PENGGUNAAN

### **Daily Tasks:**
- [ ] **Login ke Dashboard** - Akses sistem admin
- [ ] **Cek Pengajuan Surat** - Review surat yang masuk
- [ ] **Cek Pengaduan** - Review pengaduan warga
- [ ] **Cek Testimoni** - Review testimoni baru
- [ ] **Update Status** - Update status surat/pengaduan

### **Weekly Tasks:**
- [ ] **Review Berita** - Cek dan update berita
- [ ] **Export Data** - Backup data penting
- [ ] **Cek Statistik** - Review performa sistem
- [ ] **Update Konten** - Update informasi desa

### **Monthly Tasks:**
- [ ] **Review User** - Cek akses user
- [ ] **Backup Database** - Backup data lengkap
- [ ] **Update Password** - Ganti password admin
- [ ] **Review Logs** - Cek log sistem

---

**📅 Last Updated:** January 15, 2025  
**🔄 Next Review:** April 15, 2025  
**👤 Maintained By:** System Administrator  
**✅ Status:** User Ready

# 📖 USER MANUAL - SISTEM DESA CIBATU

## 🎯 Panduan Penggunaan Lengkap

**Sistem Informasi Desa Cibatu** adalah aplikasi web yang menyediakan layanan administrasi desa secara digital. Manual ini akan memandu Anda menggunakan semua fitur yang tersedia.

---

## 📋 DAFTAR ISI

- [🏠 Dashboard](#-dashboard)
- [👥 Manajemen Penduduk](#-manajemen-penduduk)
- [🏘️ Kartu Keluarga](#️-kartu-keluarga)
- [📄 Surat Keterangan](#-surat-keterangan)
- [📊 Statistik](#-statistik)
- [🌐 Web Desa](#-web-desa)
- [⚙️ Pengaturan](#️-pengaturan)
- [🔧 Troubleshooting](#-troubleshooting)

---

## 🏠 DASHBOARD

### **Akses Dashboard**
1. Buka browser dan kunjungi: `https://admin-dscibatu.sunnflower.site`
2. Login dengan username dan password yang diberikan
3. Dashboard akan menampilkan ringkasan data desa

### **Informasi Dashboard**
- **Total Penduduk**: Jumlah penduduk aktif
- **Total KK**: Jumlah kartu keluarga
- **Total RT**: Jumlah RT di desa
- **Statistik Mutasi**: Data mutasi penduduk
- **Grafik Demografi**: Visualisasi data penduduk

### **Menu Navigasi**
- **Sidebar**: Menu utama di sebelah kiri
- **Header**: Informasi user dan logout
- **Breadcrumb**: Navigasi halaman saat ini

---

## 📋 SISTEM MUTASI V3.0

### **Jenis Mutasi yang Tersedia**
- **Kelahiran**: Pendaftaran bayi baru yang lahir di desa
- **Kematian**: Pencatatan kematian penduduk desa
- **Pindah Masuk**: Penduduk baru yang pindah dari luar ke desa kita
- **Pindah Keluar**: Penduduk desa yang pindah keluar desa
- **Pindah RT/RW**: Perpindahan alamat di dalam lingkungan desa
- **Pisah KK**: Pemisahan anggota keluarga ke Kartu Keluarga (KK) lain atau baru
- **Perubahan Data**: Pembaruan informasi pada kartu keluarga atau data penduduk (misal: ganti pekerjaan, pendidikan)

### **Cara Membuat Mutasi**

#### **1. Kelahiran**
1. Klik menu **"Mutasi"** → **"Tambah Mutasi"**
2. Pilih jenis mutasi **"Kelahiran"**
3. Pilih NKK keluarga bayi
4. Isi data bayi (nama, jenis kelamin, tempat/tanggal lahir)
5. Data orang tua akan terisi otomatis
6. Klik **"Simpan"**

#### **3. Pindah Masuk**
1. Klik menu **"Mutasi"** → **"Tambah Mutasi"**
2. Pilih jenis mutasi **"Pindah Masuk"**
3. Isi data penduduk yang masuk (Nama, NIK, dll)
4. Pilih **Kategori Asal (Pindah Dari)**:
   - **Dalam Kabupaten/Kota**: Jika asal dari wilayah yang sama
   - **Luar Kabupaten/Kota**: Jika asal dari luar kota/kabupaten
   - **Luar Negeri**: Jika berasal dari luar Indonesia
5. Isi **Asal Tempat** (Nama kota atau daerah asal)
6. Klik **"Simpan"**

#### **4. Pindah Keluar**
1. Pilih jenis mutasi **"Pindah Keluar"**
2. Pilih penduduk yang akan pindah
3. Pilih **Kategori Tujuan (Pindah Ke)**:
   - **Dalam Kabupaten/Kota** / **Luar Kabupaten/Kota** / **Luar Negeri**
4. Isi **Alamat Tujuan** lengkap
5. Klik **"Simpan"**

#### **5. Pisah KK**
1. Pilih jenis mutasi **"Pisah KK"**
2. Pilih kategori: **Dalam Desa**, **Luar Kabupaten/Kota**, atau **Luar Negeri**
3. Pilih penduduk yang akan pisah KK
4. Jika **Dalam Desa**, pilih opsi:
   - **Gabung ke KK yang sudah ada**: Pilih NKK tujuan
   - **Buat KK baru**: Isi NKK baru (16 digit) dan alamat lengkap
5. Jika **Luar Desa/Kota**, isi NKK dan Alamat Tujuan lengkap
6. Klik **"Simpan"**

### **Melihat Detail Mutasi**
1. Klik menu **"Mutasi"** → **"Data Mutasi"**
2. Klik tombol **"Lihat"** pada mutasi yang diinginkan
3. Detail akan menampilkan:
   - Data penduduk terkait
   - Informasi mutasi lengkap
   - Data tracking (untuk Pisah KK luar desa)

### **Cancel/Undo Mutasi**
- **Cancel**: Untuk mutasi dalam desa (mengembalikan data ke kondisi semula)
- **Undo**: Untuk mutasi soft delete (mengembalikan penduduk yang dihapus).
- **Automated Cleanup**: Menghapus data mutasi atau pembatalan domisili akan otomatis menghapus surat terkait untuk mencegah duplikasi data sampah.

---

## 👥 MANAJEMEN PENDUDUK

### **Melihat Data Penduduk**

#### **1. Daftar Penduduk**
- Klik menu **"Data Penduduk"** di sidebar
- Tabel akan menampilkan semua data penduduk
- Data diurutkan berdasarkan KK dan kedudukan keluarga

#### **2. Pencarian Penduduk**
- Gunakan kotak **"Search"** di bagian atas
- Ketik nama, NIK, NKK, atau alamat
- Tekan Enter atau klik tombol search

#### **3. Filter Data**
- **Jenis Kelamin**: Filter Laki-laki / Perempuan
- **RT / RW / Dusun**: Pilih wilayah tertentu
- **Status**: Aktif atau Mutasi (Soft Deleted)

### **Menambah Data Penduduk**

#### **1. Form Tambah Penduduk**
- Klik tombol **"Tambah Penduduk"**
- Isi semua field yang wajib diisi (bertanda *)

#### **2. Data yang Harus Diisi**
```
* Nama Lengkap: Sesuai KTP/KK
* NIK: Nomor Induk Kependudukan (16 digit)
* NKK: Nomor Kartu Keluarga (16 digit)
* Jenis Kelamin: Laki-laki / Perempuan (Standardized)
* Tanggal Lahir: Pilih dari kalender
* Tempat Lahir: Kota/kabupaten lahir
* Alamat: Alamat lengkap saat ini
* RT/RW & Dusun: Wilayah tempat tinggal
* Kedudukan Keluarga: Kepala Keluarga, Istri, Anak, dll.
* Status Perkawinan: Belum Kawin, Kawin, Cerai Hidup, Cerai Mati
* Agama: Islam, Kristen, Katolik, Hindu, Budha, Konghucu
* Pendidikan & Pekerjaan: Pilih dari opsi yang tersedia
```

#### **3. Simpan Data**
- Klik tombol **"Simpan"**
- Sistem akan validasi data
- Jika valid, data akan tersimpan

### **Mengedit Data Penduduk**

#### **1. Akses Edit**
- Klik tombol **"Edit"** pada baris data penduduk
- Atau klik nama penduduk untuk melihat detail

#### **2. Ubah Data**
- Edit field yang perlu diubah
- Klik **"Update"** untuk menyimpan perubahan

### **Menghapus Data Penduduk**

#### **1. Soft Delete**
- Klik tombol **"Hapus"** pada baris data
- Konfirmasi penghapusan
- Data akan di-soft delete (tidak benar-benar hilang)

#### **2. Restore Data**
- Data yang dihapus bisa dikembalikan melalui menu **"Data Terhapus"**
- Klik **"Restore"** untuk mengembalikan data

### **Import/Export Data**

#### **1. Import Excel**
- Klik tombol **"Import Excel"**
- Download template Excel
- Isi data sesuai template
- Upload file Excel
- Preview dan konfirmasi import

#### **2. Export Excel**
- Klik tombol **"Export Excel"**
- Pilih format export
- Download file Excel

---

## 🏘️ KARTU KELUARGA

### **Melihat Data KK**

#### **1. Daftar Kartu Keluarga**
- Klik menu **"Kartu Keluarga"** di sidebar
- Tabel menampilkan semua KK dengan status

#### **2. Status KK**
- **Aktif**: KK dengan semua anggota aktif
- **Bermasalah**: KK dengan campuran anggota aktif dan mutasi
- **Kosong**: KK dengan semua anggota sudah mutasi

#### **3. Filter KK**
- **Search**: Cari berdasarkan NKK atau nama kepala keluarga
- **Status**: Filter berdasarkan status KK

### **Detail Kartu Keluarga**

#### **1. Melihat Detail KK**
- Klik NKK atau nama kepala keluarga
- Halaman detail akan menampilkan:
  - Informasi kepala keluarga
  - Daftar semua anggota keluarga
  - Status setiap anggota

#### **2. Informasi Anggota**
- **Nama**: Nama lengkap anggota
- **NIK**: Nomor Induk Kependudukan
- **Kedudukan**: Kedudukan dalam keluarga
- **Status**: Aktif/Meninggal/Pindah

### **Update Kepala Keluarga**

#### **1. Auto Update**
- Sistem otomatis mendeteksi KK bermasalah
- Klik tombol **"Update Kepala Keluarga"**
- Sistem akan memilih kepala keluarga baru berdasarkan prioritas:
  1. Istri
  2. Suami
  3. Anak tertua
  4. Anggota keluarga lainnya

#### **2. Manual Update**
- Klik tombol **"Update Kepala Keluarga"** pada KK bermasalah
- Pilih anggota yang akan menjadi kepala keluarga baru
- Konfirmasi perubahan

### **Batch Update**
- Klik tombol **"Batch Update Kepala Keluarga"**
- Sistem akan update semua KK bermasalah sekaligus
- Proses akan berjalan di background

---

## 📄 SURAT KETERANGAN

### **Jenis Surat yang Tersedia**

#### **1. Surat Kelahiran**
- Untuk bayi yang baru lahir
- Data yang diperlukan:
  - Nama bayi
  - Tanggal lahir
  - Jenis kelamin bayi
  - Nama ayah dan ibu
  - Tempat lahir

#### **2. Surat Kematian**
- Untuk penduduk yang meninggal
- Data yang diperlukan:
  - Nama almarhum
  - Tanggal meninggal
  - Penyebab kematian
  - Data keluarga

#### **3. Surat Keterangan Tidak Mampu (SKTM)**
- **SKTM Dewasa**: Untuk penduduk dewasa
- **SKTM Anak**: Untuk anak-anak
- Data yang diperlukan:
  - Data penduduk
  - Keperluan surat
  - Data keluarga

#### **4. Surat Keterangan Usaha (SKU)**
- Untuk penduduk yang memiliki usaha
- Data yang diperlukan:
  - Data penduduk
  - Jenis usaha
  - Alamat usaha
  - Data usaha

#### **5. Surat Domisili**
- Untuk penduduk yang pindah domisili
- Data yang diperlukan:
  - Data penduduk
  - Alamat baru
  - Data keluarga

#### **6. Surat Pindah**
- Untuk penduduk yang pindah keluar desa
- Data yang diperlukan:
  - Data penduduk
  - Tujuan pindah
  - Data keluarga

### **Membuat Surat**

#### **1. Pilih Jenis Surat**
- Klik menu **"Surat"** di sidebar
- Pilih jenis surat yang akan dibuat
- Klik tombol **"Buat Surat"**

#### **2. Isi Data Surat**
- Pilih penduduk yang bersangkutan.
- Isi data tambahan sesuai jenis surat.
- **Pilih Penandatangan**: Anda dapat memilih siapa yang akan menandatangani surat (Kepala Desa atau Sekretaris Desa) melalui dropdown yang tersedia.
- Klik **"Simpan Surat"**

#### **3. Preview dan Cetak**
- Sistem akan generate surat
- Preview surat sebelum cetak
- Klik **"Cetak PDF"** untuk download

### **Manajemen Surat**

#### **1. Daftar Surat**
- Menu **"Surat"** menampilkan semua surat
- Filter berdasarkan jenis surat
- Search berdasarkan nomor surat atau nama

#### **2. Status Surat**
- **Draft**: Surat yang belum selesai
- **Selesai**: Surat yang sudah selesai
- **Dicetak**: Surat yang sudah dicetak

#### **3. Edit Surat**
- Klik tombol **"Edit"** pada surat
- Ubah data yang diperlukan
- Simpan perubahan

---

## 📊 STATISTIK

### **Dashboard Statistik**

#### **1. Akses Statistik**
- Klik menu **"Statistik"** di sidebar
- Dashboard statistik akan menampilkan berbagai grafik

#### **2. Jenis Statistik**
- **Demografi**: Data penduduk berdasarkan usia
- **Jenis Kelamin**: Distribusi laki-laki dan perempuan
- **Pendidikan**: Tingkat pendidikan penduduk
- **Pekerjaan**: Jenis pekerjaan penduduk
- **Agama**: Distribusi agama
- **RT/RW**: Distribusi per RT/RW
- **Dusun**: Distribusi per dusun

### **Grafik dan Chart**

#### **1. Grafik Batang**
- Menampilkan data dalam bentuk batang
- Mudah dibaca dan dibandingkan
- Untuk data kategorikal

#### **2. Grafik Pie**
- Menampilkan proporsi data
- Untuk data persentase
- Mudah melihat distribusi

#### **3. Grafik Line**
- Menampilkan tren data
- Untuk data time series
- Melihat perubahan dari waktu ke waktu

### **Export Statistik**

#### **1. Export PDF**
- Klik tombol **"Export PDF"**
- Statistik akan di-generate dalam format PDF
- Download file PDF

#### **2. Export Excel**
- Klik tombol **"Export Excel"**
- Data statistik akan di-export ke Excel
- Download file Excel

---

## 🌐 WEB DESA

### **Akses Web Desa**

#### **1. URL Web Desa**
- Buka browser dan kunjungi: `https://desa-cibatu.id/web-desa`
- Web desa adalah frontend untuk warga

#### **2. Fitur Web Desa**
- **Beranda**: Informasi umum desa
- **Profil Desa**: Informasi lengkap desa
- **Layanan Surat**: Pengajuan surat online
- **Status Surat**: Cek status pengajuan
- **Berita**: Berita desa dan eksternal
- **Agenda**: Jadwal kegiatan desa
- **Transparansi**: APBDes dan proyek
- **Pengaduan**: Sistem pengaduan warga

### **Pengajuan Surat Online**

#### **1. Akses Pengajuan**
- Klik menu **"Layanan Surat"**
- Pilih jenis surat yang akan diajukan
- Klik **"Ajukan Surat"**

#### **2. Isi Form Pengajuan**
- **NIK**: Nomor Induk Kependudukan (16 digit)
- **Email**: Email untuk notifikasi
- **Tujuan**: Keperluan surat
- **Data Tambahan**: Sesuai jenis surat

#### **3. Submit Pengajuan**
- Klik **"Submit Pengajuan"**
- Sistem akan generate nomor surat
- Simpan nomor surat untuk tracking

### **Cek Status Surat**

#### **1. Akses Status Surat**
- Klik menu **"Status Surat"**
- Masukkan nomor surat atau NIK
- Klik **"Cek Status"**

#### **2. Informasi Status**
- **Pending**: Sedang diproses
- **Approved**: Sudah disetujui
- **Rejected**: Ditolak
- **Completed**: Sudah selesai

### **Berita dan Informasi**

#### **1. Berita Desa**
- Klik menu **"Berita"**
- Pilih **"Berita Desa"** untuk berita internal
- Pilih **"Berita Eksternal"** untuk berita dari media

#### **2. Agenda Desa**
- Klik menu **"Agenda"**
- Lihat jadwal kegiatan desa
- Filter berdasarkan tanggal atau kategori

### **Transparansi**

#### **1. APBDes**
- Klik menu **"Transparansi"**
- Pilih tab **"APBDes"**
- Lihat anggaran pendapatan dan belanja

#### **2. Proyek Pembangunan**
- Pilih tab **"Proyek"**
- Lihat daftar proyek pembangunan
- Status dan progress proyek

#### **3. Bantuan Sosial**
- Pilih tab **"Bantuan Sosial"**
- Lihat program bantuan sosial
- Data penerima bantuan

---

## ⚙️ PENGATURAN

### **Pengaturan Desa**

#### **1. Akses Pengaturan**
- Klik menu **"Pengaturan"** di sidebar
- Pilih tab **"Informasi Desa"**

#### **2. Data Desa**
- **Nama Desa**: Nama resmi desa
- **Kecamatan**: Nama kecamatan
- **Kabupaten**: Nama kabupaten
- **Provinsi**: Nama provinsi
- **Kode Pos**: Kode pos desa
- **Alamat Lengkap**: Alamat lengkap kantor desa
- **Telepon**: Nomor telepon kantor
- **Email**: Email resmi desa
- **Website**: Website resmi desa

#### **3. Koordinat GPS**
- **Latitude**: Koordinat lintang
- **Longitude**: Koordinat bujur
- Untuk peta di web desa

### **Struktur Organisasi**

#### **1. Kepala Desa**
- **Nama**: Nama lengkap kepala desa
- **NIP**: Nomor Induk Pegawai
- **Periode**: Masa jabatan

#### **2. Sekretaris Desa**
- **Nama**: Nama lengkap sekretaris
- **NIP**: Nomor Induk Pegawai

#### **3. Struktur Desa**
- **Kepala Dusun**: Data kepala dusun
- **Staf**: Data staf desa
- **KAUR**: Kepala Urusan (Keuangan, Perencanaan, TU)

### **Pengaturan Surat**

#### **1. Template Surat**
- Edit template surat
- Ubah header dan footer
- Kustomisasi format surat

#### **2. Penomoran Surat**
- Set format nomor surat
- Reset nomor urut tahunan
- Konfigurasi penomoran

### **Manajemen User**

#### **1. Daftar User**
- Lihat semua user yang terdaftar
- Status aktif/nonaktif
- Role dan permission

#### **2. Tambah User**
- Klik tombol **"Tambah User"**
- Isi data user
- Assign role dan permission

#### **3. Edit User**
- Klik tombol **"Edit"** pada user
- Ubah data user
- Update role dan permission

---

## 🔧 TROUBLESHOOTING

### **Masalah Umum**

#### **1. Tidak Bisa Login**
- **Penyebab**: Username/password salah
- **Solusi**: 
  - Pastikan username dan password benar
  - Cek caps lock
  - Hubungi administrator

#### **2. Halaman Tidak Load**
- **Penyebab**: Koneksi internet atau server
- **Solusi**:
  - Cek koneksi internet
  - Refresh halaman
  - Clear browser cache

#### **3. Data Tidak Tersimpan**
- **Penyebab**: Validasi data atau koneksi
- **Solusi**:
  - Cek semua field wajib diisi
  - Pastikan format data benar
  - Cek koneksi internet

#### **4. Surat Tidak Ter-generate**
- **Penyebab**: Data tidak lengkap atau template error
- **Solusi**:
  - Pastikan semua data terisi
  - Cek template surat
  - Hubungi administrator

### **Error Messages**

#### **1. "Data tidak valid"**
- Pastikan semua field wajib diisi
- Cek format data (tanggal, NIK, dll)
- Pastikan data unik (NIK tidak boleh sama)

#### **2. "Tidak memiliki permission"**
- User tidak memiliki akses ke fitur tersebut
- Hubungi administrator untuk assign permission

#### **3. "Database connection error"**
- Masalah koneksi ke database
- Hubungi administrator

### **Tips Penggunaan**

#### **1. Performance**
- Gunakan filter untuk membatasi data
- Clear browser cache secara berkala
- Tutup tab yang tidak digunakan

#### **2. Data Entry**
- Gunakan format yang benar (tanggal: YYYY-MM-DD)
- NIK harus 16 digit
- NKK harus unik per keluarga

#### **3. Security**
- Logout setelah selesai menggunakan sistem
- Jangan share username/password
- Gunakan password yang kuat

---

## 📞 BANTUAN

### **Kontak Support**
- **Email**: admin@desa-cibatu.id
- **Telepon**: (0264) 123456
- **WhatsApp**: +62 812-3456-7890
- **Alamat**: Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161

### **Jam Kerja**
- **Senin - Jumat**: 08:00 - 16:00 WIB
- **Sabtu**: 08:00 - 12:00 WIB
- **Minggu**: Tutup

### **FAQ (Frequently Asked Questions)**

#### **Q: Bagaimana cara reset password?**
A: Hubungi administrator untuk reset password.

#### **Q: Apakah data penduduk aman?**
A: Ya, data penduduk dilindungi dengan enkripsi dan backup rutin.

#### **Q: Bisakah mengakses sistem dari mobile?**
A: Ya, sistem responsive dan bisa diakses dari mobile.

#### **Q: Bagaimana cara backup data?**
A: Backup otomatis dilakukan setiap hari oleh sistem.

#### **Q: Apakah ada training untuk user baru?**
A: Ya, tersedia training dan dokumentasi lengkap.

---

## 📱 PWA (Progressive Web App)

### **Install PWA**

#### **1. Dari Browser Mobile**
- Buka `https://desa-cibatu.id` di browser mobile
- Tap tombol **"Install App"** yang muncul
- Konfirmasi instalasi

#### **2. Dari Desktop**
- Buka `https://desa-cibatu.id` di browser desktop
- Klik icon **"Install"** di address bar
- Konfirmasi instalasi

### **Fitur PWA**
- **Offline Access**: Bisa digunakan tanpa internet
- **Push Notifications**: Notifikasi real-time
- **App-like Experience**: Tampilan seperti aplikasi native
- **Background Sync**: Sync data saat online

### **Update PWA**
- PWA akan otomatis update
- Notifikasi akan muncul saat ada update
- Tap **"Update"** untuk menginstall versi terbaru

---

*Manual ini akan terus diperbarui sesuai dengan perkembangan sistem. Untuk pertanyaan atau saran, silakan hubungi tim support.*

**Terima kasih telah menggunakan Sistem Informasi Desa Cibatu!** 🙏







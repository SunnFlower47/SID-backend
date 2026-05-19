# Changelog: Migrasi Manajemen Data & Perbaikan Sistem Backup
**Tanggal:** 19 Mei 2026

## ✨ Fitur Baru & Peningkatan Antarmuka (UI)

### 1. Modul Sampah Data Penduduk (Trash)
- **Migrasi Blade ke React Inertia**: Antarmuka dipindahkan sepenuhnya dari *Blade template* ke arsitektur *React Inertia*.
- **Perubahan Nama**: Nama menu pada *Sidebar* diubah dari "Sampah Penduduk" menjadi **"Sampah Data Penduduk"** agar lebih representatif.
- **Standarisasi UI (Gold Standard)**: Menggunakan gradasi warna merah-gelap sebagai peringatan area data terhapus.
- **Interaktivitas**:
  - Penambahan fitur *Deferred Loading* dengan efek rangka berkedip (`SkeletonTable`) saat data dimuat.
  - Implementasi *debounce search* untuk pencarian NIK/Nama secara instan.
  - Penambahan konfirmasi keamanan berlapis menggunakan *SweetAlert2* pada aksi **Pulihkan** dan **Hapus Permanen**.

### 2. Modul Backup & Restore
- **Migrasi Blade ke React Inertia**: Pembaruan penuh antarmuka menggunakan komponen modern React.
- **Standarisasi UI (Gold Standard)**: Diselaraskan menggunakan gradasi warna hijau khas desain Penduduk.
- **Visualisasi Penyimpanan**: Penambahan kartu indikator sisa kapasitas *disk* / *storage server* (*Progress bar* interaktif).

### 3. Modul Export & Import Data
- **Pembaruan Header UI**: Penyelarasan tata letak *header* di halaman Export dan Import agar seragam dengan modul "Gold Standard" lainnya.
- **Animasi Export**: Mengubah cara *download* Excel dari perpindahan halaman (redirect) menjadi proses AJAX yang diiringi dengan animasi *Lottie Loading*.

## 🛠 Perbaikan Bug & Logika Backend

### 1. Modul Backup & Restore
- **[CRITICAL FIX] Perbaikan Fitur Restore**: Mengaktifkan kembali fungsi *Restore* yang sebelumnya dikomentari/tidak ada. Sistem kini mampu mengekstrak *file* `.zip` cadangan, mencari arsip `.sql`, dan menginjeksinya langsung ke MariaDB/MySQL menggunakan fungsi bawaan `DB::unprepared()`.
- **Dinamisasi Path**: Lokasi folder tujuan pencadangan data tidak lagi di-*hardcode* (`admin-panel-desa-cibatu`), melainkan secara dinamis mengambil nama aplikasi yang dikonfigurasikan dari `config('backup.backup.name')`.
- **Perbaikan Issue "mysqldump Not Recognized" (Lingkungan Lokal Windows)**:
  - Menyuntikkan kemampuan membaca `.env` lokal (`DB_DUMP_PATH`) di dalam `config/database.php` khusus untuk *driver mysql* dan *mariadb*.
  - Mengatur konfigurasi ini agar mengembalikan nilai kosong secara *default*, memastikan fungsionalitas otomatis dari Spatie berjalan normal saat di-*deploy* ke *server hosting* Linux.
  - Memastikan pembuatan *backup* di *localhost* Windows (Laragon/XAMPP) tetap lancar.

### 2. Modul Sampah Data Penduduk (Trash)
- Mengganti kembalian pengalihan aksi (dari `redirect()->route()` menjadi `back()->with()`) pada rute restorasi dan penghapusan agar mendukung notifikasi mulus *Inertia*.

# Changelog - 07 Mei 2026

## 🎯 Fokus Utama: Perbaikan Fitur Bansos & Penguncian Data

### 1. Fix Autocomplete Penduduk (Bansos)
- **Masalah:** Pencarian warga di menu Bansos tidak muncul hasil (ga ketemu), padahal di menu Surat Pengajuan lancar.
- **Solusi:** 
    - Menyamakan endpoint pencarian dengan Layanan Surat (`route('penduduk.search')`).
    - Mengembalikan `PendudukController` ke kondisi asli (revert) agar tidak merusak menu lain.
    - Meningkatkan `z-index` dropdown ke `z-[9999]` agar tidak tertutup elemen UI lain.
    - Menggunakan parameter `q` yang standar untuk pencarian NIK/Nama.

### 2. Implementasi Data Locking (Integritas Data)
- **Logika:** Jika status Bantuan Sosial adalah `selesai` atau `is_expired` (tanggal selesai sudah lewat), maka data penerima tidak boleh diubah.
- **Frontend (UI):**
    - Sembunyikan tombol "Tambah Penerima" di halaman Index.
    - Sembunyikan tombol "Edit" dan "Hapus" di baris data penerima.
    - Menambahkan Banner Peringatan "Akses Terkunci" di halaman Index dan Form.
- **Backend (Security):**
    - Menambahkan validasi di `BantuanSosialController` pada method `penerimaCreate`, `penerimaStore`, `penerimaEdit`, `penerimaUpdate`, dan `penerimaDestroy`.
    - Request akan ditolak/redirect jika mencoba memodifikasi data pada program yang sudah terkunci.

### 3. Komponen yang Terlibat
- **Shared Components:** `ResidentAutocomplete.jsx`, `MultiResidentAutocomplete.jsx`.
- **Controllers:** `BantuanSosialController.php`, `PendudukController.php`.
- **Pages:** `BantuanSosial/Penerima/Index.jsx`, `BantuanSosial/Penerima/Form.jsx`.

---
*Catatan: Masalah output karakter "3" pada API diabaikan sesuai permintaan user, namun fungsionalitas tetap berjalan karena endpoint sudah distandarisasi.*

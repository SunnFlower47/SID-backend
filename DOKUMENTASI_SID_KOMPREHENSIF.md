# 🏛️ Dokumentasi Komprehensif SID Digital - Desa Cibatu

Sistem Informasi Desa (SID) Desa Cibatu adalah platform manajemen pemerintahan desa terintegrasi yang menggabungkan kekuatan **Laravel 10** sebagai engine backend dan **React.js (Inertia.js)** sebagai antarmuka modern.

---

## 🛠️ Stack Teknologi & Arsitektur
- **Backend**: Laravel 10 (Multi-Tenant Ready).
- **Frontend**: React.js dengan Tailwind CSS (UI Gold Standard).
- **Bridge**: Inertia.js (Single Page Application experience).
- **Icons**: Lucide React & Font Awesome 6.
- **API**: RESTful API untuk integrasi Web Desa & Aplikasi Mobile.

---

## 📦 Fitur Unggulan (Core Modules)

### 1. Layanan Mandiri Surat (Citizen Service)
Fitur utama untuk mempermudah warga dalam urusan administrasi tanpa harus mengantre.
- **Verifikasi Mandiri**: Warga cukup memasukkan NIK & Tanggal Lahir (High Security - No PII Leak).
- **Pengajuan Digital**: Mendukung berbagai jenis surat (Domisili, SKTM, Kematian, dll).
- **Tracking System**: Warga bisa memantau status surat (Pending -> Diproses -> Selesai) via nomor registrasi.
- **Auto-Fill & Generate**: Admin hanya perlu memvalidasi, sistem akan mengisi data ke template (Word/PDF) secara otomatis.

### 2. Pemerintahan (Struktur Desa) - *Baru Dimigrasi ke React*
Manajemen perangkat desa dengan standar desain premium.
- **Smart Resident Search**: Memilih perangkat desa langsung dari database kependudukan menggunakan NIK (Auto-fill alamat & foto).
- **Dual Photo System**: Mendukung preview foto lama dari server dan preview instan saat baru diunggah (Base64 Stable Reader).
- **Integrasi Validasi**: Proteksi otomatis agar tidak ada NIK ganda dalam struktur pemerintahan.
- **UI Kontrol**: Toggle status aktif yang intuitif dan sinkron dengan database.

### 3. Kependudukan & Statistik
- **Manajemen KK & Penduduk**: Pengelolaan data dasar warga desa.
- **Statistik Otomatis**: Visualisasi grafik demografi (Pendidikan, Pekerjaan, Usia, Jenis Kelamin).
- **Manajemen Mutasi**: Pencatatan warga pindah, datang, dan meninggal.

---

## 🔌 Referensi API (Public & Web-Desa)
Endpoints tersedia di `app/Http/Controllers/Api/` untuk konsumsi publik.

### A. Modul Surat (`/api/v1/surat-pengajuan`)
| Method | Endpoint | Fungsi |
| :--- | :--- | :--- |
| `POST` | `/check-nik` | Verifikasi NIK + Tanggal Lahir warga |
| `POST` | `/submit` | Kirim pengajuan surat baru |
| `GET` | `/check-status` | Cek progres surat berdasarkan nomor |

### B. Modul Web Desa (`/api/v1/web-desa`)
| Method | Endpoint | Fungsi |
| :--- | :--- | :--- |
| `GET` | `/info` | Profil desa, Kades, Sekdes, & Logo |
| `GET` | `/statistics` | Data statistik kependudukan untuk dashboard |
| `GET` | `/announcements`| Berita kategori pengumuman terbaru |
| `POST` | `/submit-contact`| Kirim pesan dari form "Hubungi Kami" |

---

## 🎨 Design System: Gold Standard
Aplikasi ini mengikuti pedoman desain **Gold Standard** untuk memastikan user experience yang premium:
1. **Performance**: Menggunakan *Skeleton Loading* dan *Debounced Search* (500ms) untuk responsivitas maksimal.
2. **Aesthetics**: Palet warna harmonis (Emerald & Amber), sudut melengkung (2xl), dan bayangan lembut (shadow-sm).
3. **Responsive**: Layout adaptif yang tetap nyaman digunakan dari Desktop hingga Smartphone warga.
4. **Error Handling**: Validasi form real-time dengan label merah yang informatif.

---

## 📂 Lokasi File Penting
- **React Components**: `resources/js/Components/StrukturDesa/`
- **Inertia Pages**: `resources/js/Pages/Tenant/`
- **Controllers (Inertia)**: `app/Http/Controllers/Tenant/`
- **Controllers (API)**: `app/Http/Controllers/Api/`
- **Models**: `app/Models/`

---
*Dokumentasi ini adalah dokumen hidup yang diperbarui setiap kali ada perubahan fitur mayor.*
*Versi: 1.5.0 | Last Update: 08 Mei 2026*

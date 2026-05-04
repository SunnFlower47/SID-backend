# Audit Fitur OpenSID (Referensi Adopsi)

Dokumen ini merangkum fitur-fitur utama yang ada pada OpenSID untuk dijadikan referensi pengembangan **SID Desa Cibatu**.

---

## 👥 1. Modul Kependudukan (Master Data)
OpenSID memiliki struktur data penduduk yang sangat granular untuk kebutuhan statistik nasional (Prodeskel/SDGs).

### A. Detail Data Individu
- **Kesehatan**: Golongan darah, Status BPJS, Disabilitas (`cacat_id`), Penyakit Menahun.
- **Kelahiran**: Jam lahir, tempat lahir (RS/Bidan/Rumah), berat/panjang lahir, anak ke-berapa.
- **Legalitas**: No. Akta (Lahir, Nikah, Cerai), No. Paspor, Status Warganegara (WNI/WNA).
- **Sosial**: Suku/Etnis, Pekerja Migran (TKI/TKW), Kepesertaan Asuransi Tenaga Kerja.

### B. Pengelompokan (Grouping)
- **Keluarga (KK)**: Hubungan antar anggota dalam satu Kartu Keluarga.
- **Rumah Tangga (RTM)**: Pengelompokan warga yang tinggal serumah (penting untuk pendataan kemiskinan/ekonomi).
- **Kelompok/Lembaga**: Pendataan Karang Taruna, PKK, LPM, dll.

---

## 📄 2. Modul Layanan Surat (Administrasi)
Sistem surat OpenSID berfokus pada fleksibilitas template dan alur persetujuan.

- **Template Visual (TinyMCE)**: Edit template surat langsung di browser mirip MS Word.
- **Kode Isian Dinamis**: Admin bisa menambah field input custom di setiap jenis surat tanpa merubah kode.
- **Syarat Surat**: Checklist dokumen yang harus dibawa warga sebelum surat dicetak.
- **Penomoran Otomatis**: Pengaturan format nomor surat (Buku Agenda & Ekspedisi).
- **TTE (Digital Signature)**: Integrasi dengan BSrE untuk tanda tangan elektronik Kades.
- **Permohonan Online**: Warga bisa request surat via portal/HP.

---

## 📊 3. Modul Statistik & Laporan
Otomasi laporan yang sering diminta oleh Kecamatan/Kabupaten.

- **Statistik Real-time**: Grafik otomatis berdasarkan Pendidikan, Pekerjaan, Umur (Piramida Penduduk), Agama, dll.
- **Laporan Bulanan Desa**: Otomatis generate Laporan Bulanan (Format Lampiran A.1 - A.4).
- **Statistik Bantuan**: Grafik penerima Bansos (PKH, BPNT, BLT) agar tepat sasaran.

---

## 🏥 4. Modul Kesehatan (Posyandu & Stunting)
Modul khusus untuk mendukung program nasional penurunan stunting.

- **Data Ibu Hamil**: Tracking usia kehamilan dan jadwal pemeriksaan.
- **Data Balita (KMS Digital)**: Grafik pertumbuhan anak (Berat/Tinggi) untuk deteksi dini stunting.
- **Data Imunisasi/Vaksin**: Rekam jejak vaksinasi (termasuk Covid-19).

---

## 🏛️ 5. Modul Pertanahan (Buku C-Desa)
Fitur paling kompleks untuk pengelolaan aset tanah di tingkat desa.

- **Buku Induk C-Desa**: Digitalisasi buku besar kepemilikan tanah.
- **Klasifikasi Tanah**: Pembedaan otomatis antara Tanah Basah (Sawah) dan Tanah Kering (Daratan/Pemukiman).
- **Mutasi Tanah**: Tracking riwayat perpindahan kepemilikan tanah (Jual-beli, Waris, Hibah).
- **Data Persil**: Pendataan detail nomor persil dan lokasi tanah warga.

---

## 📂 6. Modul Sekretariat (Administrasi Umum)
Menyusun arsip digital sesuai standar regulasi Kemendagri.

- **Buku Keputusan Kades (SK)**: Pengelolaan nomor dan file digital SK Kepala Desa.
- **Buku Peraturan Desa (Perdes)**: Arsip peraturan desa yang sah.
- **Buku Ekspedisi**: Log pengiriman fisik surat keluar untuk pembukuan manual.
- **Lembaran Desa & Berita Desa**: Publikasi resmi pengumuman desa ke warga.
- **Buku Tamu & Agenda**: Pencatatan tamu kantor desa dan agenda kegiatan harian.

---

## 📱 7. Layanan Mandiri (Citizen Portal) - Deep Dive
Bukan sekadar login, tapi ekosistem interaksi warga dan perangkat.

- **Live Attendance Pamong**: Warga bisa melihat daftar perangkat desa yang sedang bertugas di kantor (Online/Offline).
- **Transparansi Bansos**: Warga bisa mengecek bantuan apa saja yang mereka terima (PKH, BPNT, BLT) untuk menghindari fitnah/kecemburuan sosial.
- **Inbox Warga**: Sarana pengaduan dan tanya jawab langsung ke admin desa.
- **Marketplace (Lapak Desa)**: Warga bisa mengunggah produk UMKM mereka untuk dipasarkan melalui website resmi desa.
- **Verifikasi OTP**: Keamanan login menggunakan Telegram atau Email untuk mencegah penyalahgunaan data NIK.

---

## 📊 8. Analisis & GIS (Mapping)
- **Analisis Kemiskinan**: Perhitungan otomatis indeks kemiskinan warga berdasarkan variabel tertentu.
- **GIS (Geographic Information System)**: Visualisasi sebaran penduduk, rumah tidak layak huni, atau titik stunting di peta desa.

---

**Rekomendasi Strategis untuk Desa Cibatu:**
1. **Modernisasi Data**: Adopsi detail data penduduk OpenSID agar dashboard statistik kita "berbicara" lebih banyak data.
2. **Citizen-Centric**: Bangun Portal Mandiri berbasis React yang kita miliki agar warga merasa dilayani secara digital (Paperless).
3. **Arsip Digital**: Mulai digitalisasi SK Kades dan Perdes agar administrasi desa lebih akuntabel.

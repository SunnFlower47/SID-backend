# Changelog - 6 Mei 2026

## [FITUR BARU]
- **Modul Bantuan Sosial (Bansos) Lengkap:**
  - **Manajemen Program:** CRUD penuh untuk program bantuan sosial desa.
  - **Manajemen Penerima (Bulk):** Kemampuan menambahkan banyak penerima sekaligus ke dalam satu program.
  - **Sistem Pembayaran Fleksibel:** Dukungan untuk pembayaran sekali cair atau berkala (4 tahapan otomatis).
  - **Dashboard Analytics:** Statistik real-time jumlah program dan penerima aktif di halaman indeks.
  - **Cek Bansos via NIK:** API endpoint khusus untuk pengecekan status bantuan warga berdasarkan NIK.

## [KOMPONEN UI BARU]
- **MultiResidentAutocomplete:** Komponen input pencarian warga yang mendukung pemilihan ganda (multi-select).
- **BansosStats & Filters:** Komponen visual untuk statistik cepat dan penyaringan data bantuan.
- **Form Penerima Dinamis:** Form yang menyesuaikan input berdasarkan pilihan sistem pembayaran (sekali vs berkala).

## [TEKNIS]
- Implementasi `StoreBantuanSosialRequest`, `UpdateBantuanSosialRequest`, dll untuk validasi data yang ketat.
- Logika pembagian nominal otomatis untuk pembayaran berkala dengan penanganan nilai sisa (`remainder`).
- Eager Loading pada relasi `penerima.penduduk` untuk optimasi performa query.

---
**Status Sesi:** SELESAI (Major Update)
**Dikerjakan oleh:** User (Manual Update) & AI (Documentation)

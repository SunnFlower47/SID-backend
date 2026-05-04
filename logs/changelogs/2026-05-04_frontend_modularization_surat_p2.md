# Changelog: Frontend Modularization (Surat Pengajuan)

**Date:** 2026-05-04  
**Author:** Antigravity  
**Version:** v1.2.0-beta  

## Description
Berhasil melakukan modularisasi pada komponen frontend untuk modul Surat Pengajuan. Refactoring ini memecah file monolithic `Create.jsx` dan `Edit.jsx` menjadi komponen-komponen kecil yang reusable dan mudah dikelola.

## Changes
### [RESOURCES] `js/Pages/Tenant/SuratPengajuan/`
- **[NEW]** `Components/TypeSelector.jsx`: Komponen seleksi jenis surat.
- **[NEW]** `Components/ResidentSearch.jsx`: Komponen pencarian penduduk terpadu.
- **[NEW]** `Components/ManualDomisiliForm.jsx`: Form input data manual penduduk luar desa.
- **[NEW]** `Components/KematianForm.jsx`: Form detail mutasi kematian.
- **[MODIFY]** `Create.jsx`: Menggunakan komponen modular, mengurangi ukuran file dari ~900 baris menjadi ~500 baris.
- **[MODIFY]** `Edit.jsx`: Menggunakan komponen modular, memastikan konsistensi UI antara form Create dan Edit.

## Technical Details
- Menggunakan `lucide-react` untuk konsistensi ikon.
- Mempertahankan state `updateDataTambahan` dari parent component untuk sinkronisasi data yang aman.
- Implementasi `animate-in` Tailwind untuk transisi UI yang halus saat pergantian jenis surat.

## Impact
- **Maintainability**: Kode jauh lebih mudah dibaca dan diperbaiki.
- **Reusability**: Komponen dapat digunakan di bagian lain aplikasi jika diperlukan.
- **Performance**: Struktur komponen yang lebih terfokus membantu proses rendering React.

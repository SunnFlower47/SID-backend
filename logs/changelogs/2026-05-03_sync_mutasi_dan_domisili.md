# Change Log - 2026-05-03
## Topic: Sinkronisasi Mutasi Kematian & Optimalisasi Form Domisili

### [ADDED]
- Menambahkan input field **"Kota / Kabupaten Asal"** (`asal_daerah`) pada form Pembuatan Surat Domisili (`Create.jsx`) dan Edit Surat Domisili (`Edit.jsx`).
- Menambahkan kolom **"Wilayah / Kota Asal"** pada Detail Modal di menu Penduduk Domisili.

### [FIXED]
- **Undo Mutasi Kematian**: Menambahkan logic otomatis untuk menghapus surat kematian terkait saat mutasi kematian di-undo.
- **Sync Logic**: Menghubungkan ID surat pengajuan ke dalam log mutasi (`detail_tambahan`) untuk tracking yang lebih akurat.
- **Fallback Search**: Implementasi pencarian fallback berdasarkan `penduduk_id` untuk data mutasi lama yang belum memiliki link ID surat.
- **UI/UX Domisili**: Menambahkan fallback tampilan "Alamat Asal" di tabel jika "Asal Daerah" kosong, agar tidak muncul tanda strip (`-`).
- **UI Search**: Memperbaiki z-index/overflow pada card pencarian penduduk agar tidak terpotong saat muncul dropdown hasil pencarian.

### [TECHNICAL]
- Update `MutasiService@handleKematian` untuk menyimpan `surat_pengajuan_id`.
- Update `MutasiController@undo` untuk handling pembersihan surat.
- Refaktor `PendudukDomisili` model accessors.

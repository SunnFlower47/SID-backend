/**
 * Konstanta keuangan desa sesuai Permendagri No. 20 Tahun 2018
 * Digunakan di semua halaman APBDes (Create, Edit, Index, Filter)
 */

export const BIDANG_LIST = [
    { value: 1, label: 'Bid. 1 — Penyelenggaraan Pemerintahan Desa' },
    { value: 2, label: 'Bid. 2 — Pelaksanaan Pembangunan Desa' },
    { value: 3, label: 'Bid. 3 — Pembinaan Kemasyarakatan Desa' },
    { value: 4, label: 'Bid. 4 — Pemberdayaan Masyarakat Desa' },
    { value: 5, label: 'Bid. 5 — Penanggulangan Bencana, Kedaruratan & Mendesak' },
];

export const BIDANG_MAP = {
    1: 'Pemerintahan',
    2: 'Pembangunan',
    3: 'Pembinaan',
    4: 'Pemberdayaan',
    5: 'Bencana & Mendesak',
};

export const BIDANG_COLOR = {
    1: { bg: 'bg-violet-50', text: 'text-violet-700', border: 'border-violet-100', dot: '#8b5cf6' },
    2: { bg: 'bg-blue-50',   text: 'text-blue-700',   border: 'border-blue-100',   dot: '#3b82f6' },
    3: { bg: 'bg-pink-50',   text: 'text-pink-700',   border: 'border-pink-100',   dot: '#ec4899' },
    4: { bg: 'bg-teal-50',   text: 'text-teal-700',   border: 'border-teal-100',   dot: '#14b8a6' },
    5: { bg: 'bg-red-50',    text: 'text-red-700',    border: 'border-red-100',    dot: '#ef4444' },
};

// Sub-Bidang per Bidang (Permendagri 20/2018 — dipersingkat agar UX tidak terlalu panjang)
export const SUB_BIDANG = {
    1: [
        { value: '1.1', label: '1.1 — Penyelenggaraan Belanja Siltap, Tunjangan & Op. Pemdes' },
        { value: '1.2', label: '1.2 — Sarana & Prasarana Pemerintahan Desa' },
        { value: '1.3', label: '1.3 — Administrasi Kependudukan, Pencatatan Sipil & Statistik' },
        { value: '1.4', label: '1.4 — Tata Praja Pemerintahan, Perencanaan, Keuangan & Pelaporan' },
        { value: '1.5', label: '1.5 — Pertanahan' },
    ],
    2: [
        { value: '2.1', label: '2.1 — Pendidikan' },
        { value: '2.2', label: '2.2 — Kesehatan' },
        { value: '2.3', label: '2.3 — Pekerjaan Umum & Penataan Ruang' },
        { value: '2.4', label: '2.4 — Kawasan Permukiman' },
        { value: '2.5', label: '2.5 — Kehutanan & Lingkungan Hidup' },
        { value: '2.6', label: '2.6 — Perhubungan, Komunikasi & Informatika' },
        { value: '2.7', label: '2.7 — Energi & Sumber Daya Mineral' },
        { value: '2.8', label: '2.8 — Pariwisata' },
    ],
    3: [
        { value: '3.1', label: '3.1 — Ketentraman, Ketertiban Umum & Perlindungan Masyarakat' },
        { value: '3.2', label: '3.2 — Kebudayaan & Keagamaan' },
        { value: '3.3', label: '3.3 — Kepemudaan & Olahraga' },
        { value: '3.4', label: '3.4 — Kelembagaan Masyarakat' },
    ],
    4: [
        { value: '4.1', label: '4.1 — Kelautan & Perikanan' },
        { value: '4.2', label: '4.2 — Pertanian & Peternakan' },
        { value: '4.3', label: '4.3 — Peningkatan Kapasitas Aparatur Desa' },
        { value: '4.4', label: '4.4 — Pemberdayaan Perempuan, Perlindungan Anak & KB' },
        { value: '4.5', label: '4.5 — Koperasi, Usaha Mikro Kecil & Menengah' },
        { value: '4.6', label: '4.6 — Dukungan Penanaman Modal' },
        { value: '4.7', label: '4.7 — Perdagangan & Perindustrian' },
    ],
    5: [
        { value: '5.1', label: '5.1 — Penanggulangan Bencana' },
        { value: '5.2', label: '5.2 — Keadaan Darurat' },
        { value: '5.3', label: '5.3 — Keadaan Mendesak' },
    ],
};

export const SUMBER_DANA_LIST = [
    { group: 'Dana Transfer Pusat',
      options: [
          { value: 'dana_desa_ad', label: 'Dana Desa - Alokasi Dasar (AD)' },
          { value: 'dana_desa_af', label: 'Dana Desa - Alokasi Formula (AF)' },
          { value: 'dana_desa_ak', label: 'Dana Desa - Alokasi Kinerja (AK)' },
      ]
    },
    { group: 'Dana Transfer Daerah',
      options: [
          { value: 'add',              label: 'Alokasi Dana Desa (ADD)' },
          { value: 'bhpr',             label: 'Bagi Hasil Pajak & Retribusi (BHPR)' },
          { value: 'bantuan_keuangan', label: 'Bantuan Keuangan APBD Prov/Kab' },
      ]
    },
    { group: 'Dana Lain',
      options: [
          { value: 'dau', label: 'Dana Alokasi Umum (DAU)' },
          { value: 'dak', label: 'Dana Alokasi Khusus (DAK)' },
          { value: 'dbh', label: 'Dana Bagi Hasil (DBH)' },
          { value: 'did', label: 'Dana Insentif Daerah (DID)' },
      ]
    },
    { group: 'Pendapatan Desa',
      options: [
          { value: 'pad',        label: 'Pendapatan Asli Desa (PAD)' },
          { value: 'hibah',      label: 'Hibah & Sumbangan Pihak Ketiga' },
          { value: 'lain_lain',  label: 'Lain-Lain PADes yang Sah' },
      ]
    },
];

// Flatten untuk dropdown sederhana
export const SUMBER_DANA_FLAT = SUMBER_DANA_LIST.flatMap(g => g.options);

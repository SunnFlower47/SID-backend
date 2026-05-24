<?php

return [
    'Kependudukan & Wilayah' => [
        'penduduk.view', 'penduduk.create', 'penduduk.edit', 'penduduk.delete', 'penduduk.export', 'penduduk.import', 'penduduk.restore', 'penduduk.force_delete',
        'penduduk_domisili.view', 'penduduk_domisili.create', 'penduduk_domisili.edit', 'penduduk_domisili.delete',
        'kartu_keluarga.view', 'kartu_keluarga.create', 'kartu_keluarga.edit', 'kartu_keluarga.delete', 'kartu_keluarga.export',
        'mutasi.view', 'mutasi.create', 'mutasi.edit', 'mutasi.delete', 'mutasi.undo', 'mutasi.cancel', 'mutasi.print',
        'pisah-kk.view', 'pisah-kk.create', 'pisah-kk.process',
        'wilayah.view', 'wilayah.manage', 'wilayah.import_conflict.manage',
    ],
    'Pelayanan Masyarakat' => [
        'surat.view', 'surat.create', 'surat.edit', 'surat.delete', 'surat.export',
        'surat_type.view', 'surat_type.create', 'surat_type.edit', 'surat_type.delete',
        'pengaduan.view', 'pengaduan.create', 'pengaduan.edit', 'pengaduan.delete', 'pengaduan.manage', 'pengaduan.export',
        'bantuan_sosial.view', 'bantuan_sosial.create', 'bantuan_sosial.edit', 'bantuan_sosial.delete', 'bantuan_sosial.manage_penerima', 'bantuan_sosial.export', 'bantuan_sosial.import',
        'contact-messages.view', 'contact-messages.manage', 'contact-messages.delete',
    ],
    'Ekonomi & Konten Desa' => [
        'umkm.view', 'umkm.create', 'umkm.edit', 'umkm.delete', 'umkm.export', 'umkm.import',
        'berita.view', 'berita.create', 'berita.edit', 'berita.delete',
        'testimoni.view', 'testimoni.create', 'testimoni.update', 'testimoni.delete',
        'fasilitas-desa.view', 'fasilitas-desa.create', 'fasilitas-desa.edit', 'fasilitas-desa.delete',
        'struktur-desa.view', 'struktur-desa.create', 'struktur-desa.edit', 'struktur-desa.delete',
        'master-jabatan.view', 'master-jabatan.create', 'master-jabatan.edit', 'master-jabatan.delete',
        'kontak-desa.view', 'kontak-desa.create', 'kontak-desa.edit', 'kontak-desa.delete',
        'village-profile.view', 'village-profile.edit',
    ],
    'Aset & Keuangan' => [
        'aset_barang.view', 'aset_barang.create', 'aset_barang.edit', 'aset_barang.delete',
        'aset_inventaris.view', 'aset_inventaris.create', 'aset_inventaris.edit', 'aset_inventaris.delete',
        'aset_mutasi.view', 'aset_mutasi.create', 'aset_mutasi.edit', 'aset_mutasi.delete',
        'anggaran.view', 'anggaran.create', 'anggaran.edit', 'anggaran.delete',
        'laporan_keuangan.view', 'laporan_keuangan.export',
        'peraturan_desa.view', 'peraturan_desa.create', 'peraturan_desa.edit', 'peraturan_desa.delete',
        'transparansi-desa.view', 'transparansi-desa.create', 'transparansi-desa.edit', 'transparansi-desa.delete',
    ],
    'Laporan & Statistik' => [
        'laporan.view', 'laporan.penduduk', 'laporan.mutasi', 'laporan.pisah_kk', 'laporan.export',
        'statistics.view', 'comparison.view',
    ],
    'Admin Sistem' => [
        'settings.view', 'settings.edit', 'settings.export', 'settings.import',
        'users.manage', 'users.create', 'users.edit', 'users.delete',
        'roles.manage', 'roles.create', 'roles.edit', 'roles.delete',
        'audit_log.view', 'audit_log.export',
        'backup.view', 'backup.manage', 'backup.create', 'backup.download', 'backup.restore', 'backup.delete', 'backup.export',
    ]
];

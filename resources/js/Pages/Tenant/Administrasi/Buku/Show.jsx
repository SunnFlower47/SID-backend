import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge } from '@/Components/Shared';
import { FileBadge, Search, Download, Printer, Filter, Calendar } from 'lucide-react';

// ─── Helper: format angka ────────────────────────────────────────────────────
const fmtNum = (n) => n > 0 ? new Intl.NumberFormat('id-ID').format(n) : '-';

// ─── Tabel 16 Kolom Permendagri ──────────────────────────────────────────────
function InventarisTable({ rows, tahun }) {
    const TH = ({ children, rowSpan, colSpan, className = '' }) => (
        <th
            rowSpan={rowSpan}
            colSpan={colSpan}
            className={`border border-slate-400 px-2 py-1.5 text-center text-[9px] font-black uppercase tracking-wide bg-slate-100 text-slate-700 leading-tight ${className}`}
        >
            {children}
        </th>
    );
    const TD = ({ children, center = false, mono = false, bold = false, muted = false }) => (
        <td className={`border border-slate-300 px-1.5 py-1.5 text-[9px] leading-tight align-middle
            ${center ? 'text-center' : 'text-left'}
            ${mono ? 'font-mono' : ''}
            ${bold ? 'font-bold' : ''}
            ${muted ? 'text-slate-400' : 'text-slate-700'}`}>
            {children}
        </td>
    );

    return (
        <div className="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
            {/* Badge tahun */}
            <div className="flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700">
                <span className="text-[10px] font-black text-white uppercase tracking-widest">
                    Buku Inventaris &amp; Kekayaan Desa — Tahun {tahun}
                </span>
                <span className="text-[9px] font-bold text-blue-200">Permendagri No. 47 / 2016</span>
            </div>

            <table className="w-full border-collapse" style={{ minWidth: '1400px' }}>
                <thead>
                    {/* ── Baris 1: Grup Besar ── */}
                    <tr>
                        <TH rowSpan={3} className="w-8">NO</TH>
                        <TH rowSpan={3} className="w-48 text-left px-3">JENIS BARANG / BANGUNAN</TH>
                        <TH colSpan={5}>ASAL BARANG / BANGUNAN</TH>
                        <TH colSpan={2}>KEADAAN BARANG /<br/>BANGUNAN AWAL TAHUN</TH>
                        <TH colSpan={4}>PENGHAPUSAN BARANG DAN BANGUNAN</TH>
                        <TH colSpan={2}>KEADAAN BARANG /<br/>BANGUNAN AKHIR TAHUN</TH>
                        <TH rowSpan={3} className="w-20">KET.</TH>
                    </tr>
                    {/* ── Baris 2: Sub Grup ── */}
                    <tr>
                        <TH rowSpan={2} className="w-16">DIBELI<br/>SENDIRI</TH>
                        <TH colSpan={3}>BANTUAN</TH>
                        <TH rowSpan={2} className="w-16">SUMBANGAN</TH>
                        <TH rowSpan={2} className="w-14">BAIK</TH>
                        <TH rowSpan={2} className="w-14">RUSAK</TH>
                        <TH rowSpan={2} className="w-14">RUSAK</TH>
                        <TH rowSpan={2} className="w-14">DIJUAL</TH>
                        <TH rowSpan={2} className="w-20">DISUMBANG­KAN</TH>
                        <TH rowSpan={2} className="w-20">TGL<br/>PENGHAPUSAN</TH>
                        <TH rowSpan={2} className="w-14">BAIK</TH>
                        <TH rowSpan={2} className="w-14">RUSAK</TH>
                    </tr>
                    {/* ── Baris 3: Sub-sub grup Bantuan ── */}
                    <tr>
                        <TH className="w-16">PEMERINTAH<br/>(PUSAT)</TH>
                        <TH className="w-16">PROVINSI</TH>
                        <TH className="w-16">KAB/<br/>KOTA</TH>
                    </tr>
                    {/* ── Baris 4: Nomor kolom ── */}
                    <tr className="bg-slate-50">
                        {[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16].map(n => (
                            <td key={n} className="border border-slate-300 text-center text-[9px] font-black text-blue-600 py-1 italic">
                                {n}
                            </td>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {rows.length === 0 ? (
                        <tr>
                            <td colSpan={16} className="border border-slate-300 text-center py-10 text-xs text-slate-400 font-medium">
                                Tidak ada data inventaris untuk tahun ini.
                            </td>
                        </tr>
                    ) : (
                        rows.map((row, i) => (
                            <tr
                                key={row.id ?? i}
                                className={i % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'}
                            >
                                <TD center>{i + 1}</TD>
                                <td className="border border-slate-300 px-2 py-1.5 text-[9px] align-middle">
                                    <div className="font-bold text-slate-800 leading-tight">{row.nama_barang}</div>
                                    <div className="font-mono text-slate-400 text-[8px] mt-0.5">{row.kode_barang}</div>
                                    {row.lokasi && <div className="text-slate-400 text-[8px]">{row.lokasi}</div>}
                                </td>
                                {/* Kolom 3-7: Asal Barang */}
                                <TD center mono>{fmtNum(row.asal_dibeli)}</TD>
                                <TD center mono>{fmtNum(row.asal_bantuan_pusat)}</TD>
                                <TD center mono>{fmtNum(row.asal_bantuan_prov)}</TD>
                                <TD center mono>{fmtNum(row.asal_bantuan_kab)}</TD>
                                <TD center mono>{fmtNum(row.asal_sumbangan)}</TD>
                                {/* Kolom 8-9: Keadaan Awal */}
                                <TD center mono>{fmtNum(row.awal_baik)}</TD>
                                <TD center mono>{fmtNum(row.awal_rusak)}</TD>
                                {/* Kolom 10-13: Penghapusan */}
                                <TD center mono>{fmtNum(row.hapus_rusak)}</TD>
                                <TD center mono>{fmtNum(row.hapus_dijual)}</TD>
                                <TD center mono>{fmtNum(row.hapus_disumbangkan)}</TD>
                                <TD center muted={row.tgl_penghapusan === '-'}>{row.tgl_penghapusan}</TD>
                                {/* Kolom 14-15: Keadaan Akhir */}
                                <TD center mono bold>{fmtNum(row.akhir_baik)}</TD>
                                <TD center mono bold>{fmtNum(row.akhir_rusak)}</TD>
                                {/* Kolom 16: Keterangan */}
                                <TD muted={!row.keterangan}>{row.keterangan || '-'}</TD>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>

            {/* Footer info */}
            <div className="px-4 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span className="text-[9px] text-slate-400 font-semibold">
                    Total {rows.length} jenis barang · Scroll horizontal untuk melihat semua kolom
                </span>
                <span className="text-[9px] text-slate-400 font-semibold">
                    Angka menunjukkan kuantitas barang
                </span>
            </div>
        </div>
    );
}

// ─── Komponen Utama ──────────────────────────────────────────────────────────
export default function ShowBuku({ auth, jenis_buku, data, filters }) {
    const currentYear = new Date().getFullYear();
    const [search, setSearch] = useState(filters.search || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');
    const [tahun, setTahun] = useState(filters.tahun || String(currentYear));

    const isInventaris = jenis_buku === 'inventaris-kekayaan';

    const handleFilter = (e) => {
        e.preventDefault();
        const params = isInventaris
            ? { tahun }
            : { search, start_date: startDate, end_date: endDate };
        router.get(route('administrasi.buku.show', jenis_buku), params, { preserveState: true });
    };

    const handleReset = () => {
        setSearch(''); setStartDate(''); setEndDate('');
        setTahun(String(currentYear));
        router.get(route('administrasi.buku.show', jenis_buku));
    };

    const tahunOptions = Array.from({ length: 6 }, (_, i) => currentYear - i);

    const getJudulBuku = () => {
        switch (jenis_buku) {
            case 'keputusan-kades':     return 'Buku Keputusan Kepala Desa';
            case 'peraturan-desa':      return 'Buku Peraturan di Desa';
            case 'buku-agenda':         return 'Buku Agenda';
            case 'inventaris-kekayaan': return 'Buku Inventaris dan Kekayaan Desa';
            case 'aparat-pemerintah':   return 'Buku Aparat Pemerintah Desa';
            case 'tanah-kas-desa':      return 'Buku Tanah Kas Desa';
            case 'tanah-di-desa':       return 'Buku Tanah di Desa';
            default:                    return 'Buku Administrasi';
        }
    };

    // Untuk inventaris: data adalah flat array langsung (bukan paginated)
    const inventarisRows = isInventaris ? (Array.isArray(data) ? data : Object.values(data)) : [];

    // Untuk buku lain: data adalah paginated object
    const paginatedData = !isInventaris ? data : null;

    // ── Header dan body untuk buku non-inventaris ──
    const renderTableHead = () => {
        switch (jenis_buku) {
            case 'peraturan-desa':
                return (
                    <tr className="bg-gray-50/80 border-b border-gray-100">
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Jenis Peraturan</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nomor &amp; Tanggal</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Tentang</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Status</th>
                    </tr>
                );
            case 'aparat-pemerintah':
                return (
                    <tr className="bg-gray-50/80 border-b border-gray-100">
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama &amp; NIP</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Pangkat/Jabatan</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Tanggal SK</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Status</th>
                    </tr>
                );
            case 'tanah-kas-desa':
                return (
                    <tr className="bg-gray-50/80 border-b border-gray-100">
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama / Jenis Tanah</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Lokasi</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Luas (m²)</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">No. Sertifikat</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Asal Usul</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Kondisi</th>
                    </tr>
                );
            case 'buku-agenda':
                return (
                    <tr className="bg-gray-50/80 border-b border-gray-100">
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Tanggal Catat</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Informasi Surat</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Pengirim / Penerima</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Isi Singkat</th>
                    </tr>
                );
            case 'tanah-di-desa':
                return (
                    <tr className="bg-gray-50/80 border-b border-gray-100">
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama Pemilik</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Luas (m²)</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Rincian Hak & Penggunaan</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Keterangan</th>
                    </tr>
                );
            case 'keputusan-kades':
            default:
                return (
                    <tr className="bg-gray-50/80 border-b border-gray-100">
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest w-48">Nomor Keputusan</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Tentang / Uraian Singkat</th>
                        <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-32">Tgl Ditetapkan</th>
                        <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Keterangan</th>
                    </tr>
                );
        }
    };

    const renderTableBody = () => {
        const formatDate = (dateString) => {
            if (!dateString || dateString === '-') return '-';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric'
                }).format(date);
            } catch {
                return dateString;
            }
        };

        return paginatedData.data.map((item, index) => {
            const no = paginatedData.from + index;
            switch (jenis_buku) {
                case 'peraturan-desa':
                    return (
                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
                            <td className="px-4 py-3 font-bold text-gray-900">{item.jenis_peraturan}</td>
                            <td className="px-4 py-3">
                                <div className="font-mono text-gray-900">{item.nomor_peraturan || '-'}</div>
                                <div className="text-xs text-gray-500">{formatDate(item.tanggal_ditetapkan)}</div>
                            </td>
                            <td className="px-4 py-3 text-gray-800">{item.judul}</td>
                            <td className="px-4 py-3 text-center">
                                <Badge color={item.status === 'disetujui' ? 'green' : 'amber'}>{item.status}</Badge>
                            </td>
                        </tr>
                    );
                case 'tanah-kas-desa': {
                    const kondisiLabel = { baik: '✅ Baik', rusak_ringan: '⚠️ Rusak Ringan', rusak_berat: '❌ Rusak Berat' };
                    return (
                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
                            <td className="px-4 py-3">
                                <div className="font-bold text-gray-900">{item.nama_barang_override || item.barang?.nama_barang}</div>
                                <div className="font-mono text-xs text-gray-400">{item.barang?.kode_barang || '-'}</div>
                            </td>
                            <td className="px-4 py-3 text-gray-700">{item.lokasi || '-'}</td>
                            <td className="px-4 py-3 text-center font-mono font-bold text-gray-800">
                                {item.saldo_kwantitas > 0 ? new Intl.NumberFormat('id-ID').format(item.saldo_kwantitas) : '-'}
                            </td>
                            <td className="px-4 py-3 text-center font-mono text-xs text-gray-600">{item.no_sertifikat || '-'}</td>
                            <td className="px-4 py-3 text-center text-xs font-semibold text-gray-700">{item.asal_usul || '-'}</td>
                            <td className="px-4 py-3 text-center text-xs">{kondisiLabel[item.kondisi] || item.kondisi || '-'}</td>
                        </tr>
                    );
                }
                case 'aparat-pemerintah':
                    return (
                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
                            <td className="px-4 py-3">
                                <div className="font-bold text-gray-900">{item.nama}</div>
                                <div className="font-mono text-xs text-gray-500">{item.nik || '-'}</div>
                            </td>
                            <td className="px-4 py-3 text-gray-800 uppercase text-xs font-bold">{item.jabatan}</td>
                            <td className="px-4 py-3 text-center">{formatDate(item.tanggal_pengangkatan)}</td>
                            <td className="px-4 py-3">
                                <Badge color={item.status_aktif ? 'green' : 'red'}>{item.status_aktif ? 'Aktif' : 'Non-Aktif'}</Badge>
                            </td>
                        </tr>
                    );
                case 'buku-agenda':
                    return (
                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
                            <td className="px-4 py-3">
                                <div className="font-bold text-gray-900">{formatDate(item.tanggal)}</div>
                                <div className="mt-1">
                                    {item.jenis_surat === 'Masuk' ? (
                                        <Badge color="green">Surat Masuk</Badge>
                                    ) : (
                                        <Badge color="blue">Surat Keluar</Badge>
                                    )}
                                </div>
                            </td>
                            <td className="px-4 py-3">
                                <div className="font-bold text-gray-900">{item.nomor_surat || '-'}</div>
                                <div className="text-xs text-gray-500">Tgl: {formatDate(item.tanggal_surat)}</div>
                            </td>
                            <td className="px-4 py-3">
                                <div className="text-gray-900 font-medium truncate max-w-xs">{item.pengirim_penerima}</div>
                                {item.keterangan && <div className="text-xs text-gray-500 truncate max-w-xs">{item.keterangan}</div>}
                            </td>
                            <td className="px-4 py-3">
                                <div className="text-gray-700 max-w-xs line-clamp-2">{item.isi_singkat}</div>
                            </td>
                        </tr>
                    );
                case 'tanah-di-desa':
                    return (
                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
                            <td className="px-4 py-3 font-bold text-gray-900">{item.nama_pemilik}</td>
                            <td className="px-4 py-3 text-center font-mono font-bold text-gray-800">
                                {new Intl.NumberFormat('id-ID').format(item.luas_tanah)}
                            </td>
                            <td className="px-4 py-3">
                                <div className="text-xs text-gray-500 mb-1">
                                    <span className="font-bold text-gray-700">Hak Milik: </span>{item.status_hm > 0 ? `${new Intl.NumberFormat('id-ID').format(item.status_hm)} m²` : '-'}
                                </div>
                                <div className="text-xs text-gray-500">
                                    <span className="font-bold text-gray-700">Tanah Desa: </span>{item.status_td > 0 ? `${new Intl.NumberFormat('id-ID').format(item.status_td)} m²` : '-'}
                                </div>
                            </td>
                            <td className="px-4 py-3 text-gray-600">{item.keterangan || '-'}</td>
                        </tr>
                    );
                case 'keputusan-kades':
                default:
                    return (
                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
                            <td className="px-4 py-3 font-mono font-bold text-gray-900">{item.nomor_keputusan}</td>
                            <td className="px-4 py-3 text-gray-800">{item.judul_keputusan}</td>
                            <td className="px-4 py-3 text-center"><Badge color="blue">{formatDate(item.tanggal_ditetapkan)}</Badge></td>
                            <td className="px-4 py-3 text-gray-600">{item.keterangan || '-'}</td>
                        </tr>
                    );
            }
        });
    };

    const pdfUrl = isInventaris
        ? route('administrasi.buku.export.pdf', jenis_buku) + `?tahun=${tahun}`
        : route('administrasi.buku.export.pdf', jenis_buku);

    const actions = [
        { label: 'Cetak PDF', icon: Printer, href: pdfUrl, variant: 'white', external: true },
        { label: 'Unduh Excel', icon: Download, href: route('administrasi.buku.export.excel', jenis_buku), variant: 'ghost', external: true },
    ];

    return (
        <AuthenticatedLayout user={auth.user} title={getJudulBuku()}>
            <Head title={`${getJudulBuku()} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={FileBadge}
                    title={getJudulBuku()}
                    subtitle={isInventaris ? `Format Permendagri 47/2016 · Tahun ${tahun}` : 'Pratinjau data buku administrasi resmi'}
                    actions={actions}
                />

                {/* ── Filter ── */}
                <form onSubmit={handleFilter} className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4">
                    {isInventaris ? (
                        <div>
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Pilih Tahun</label>
                            <select
                                className="w-40 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 font-bold"
                                value={tahun}
                                onChange={(e) => setTahun(e.target.value)}
                            >
                                {tahunOptions.map(y => (
                                    <option key={y} value={y}>{y}</option>
                                ))}
                            </select>
                        </div>
                    ) : (
                        <>
                            <div className="flex-1">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Pencarian</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input type="text" placeholder="Pencarian..."
                                        className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500"
                                        value={search} onChange={(e) => setSearch(e.target.value)} />
                                </div>
                            </div>
                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Dari Tanggal</label>
                                <div className="relative">
                                    <Calendar className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input type="date"
                                        className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500"
                                        value={startDate} onChange={(e) => setStartDate(e.target.value)} />
                                </div>
                            </div>
                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Sampai Tanggal</label>
                                <div className="relative">
                                    <Calendar className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input type="date"
                                        className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500"
                                        value={endDate} onChange={(e) => setEndDate(e.target.value)} />
                                </div>
                            </div>
                        </>
                    )}
                    <div className="flex items-end gap-2">
                        <button type="submit" className="px-6 py-2 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-colors">
                            {isInventaris ? 'Tampilkan' : 'Filter Data'}
                        </button>
                        {(search || startDate || endDate) && (
                            <button type="button" onClick={handleReset} className="px-4 py-2 bg-gray-100 text-gray-500 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-colors">
                                Reset
                            </button>
                        )}
                    </div>
                </form>

                {/* ── Tabel Inventaris 16 Kolom ── */}
                {isInventaris && (
                    <InventarisTable rows={inventarisRows} tahun={tahun} />
                )}

                {/* ── Tabel Buku Lain (Paginated) ── */}
                {!isInventaris && paginatedData && (
                    <TableCard
                        icon={FileBadge}
                        title="Pratinjau Data"
                        total={paginatedData.total}
                        totalLabel="Data"
                    >
                        {paginatedData.data.length === 0 ? (
                            <EmptyState
                                title="Data Kosong"
                                message={`Tidak ada data ${getJudulBuku()} yang ditemukan.`}
                                icon={Filter}
                            />
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-xs min-w-[1000px]">
                                    <thead>{renderTableHead()}</thead>
                                    <tbody>{renderTableBody()}</tbody>
                                </table>
                            </div>
                        )}
                        {paginatedData.links && paginatedData.links.length > 3 && (
                            <div className="p-4 border-t border-gray-100 flex justify-center">
                                <div className="flex gap-1">
                                    {paginatedData.links.map((link, i) => (
                                        <Link
                                            key={i}
                                            href={link.url || '#'}
                                            className={`px-3 py-1.5 text-xs font-bold rounded-lg border ${
                                                link.active
                                                    ? 'bg-blue-600 text-white border-blue-600'
                                                    : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'
                                            } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </TableCard>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

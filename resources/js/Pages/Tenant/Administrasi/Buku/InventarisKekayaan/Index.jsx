import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

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
                                <TD center mono>{fmtNum(row.asal_dibeli)}</TD>
                                <TD center mono>{fmtNum(row.asal_bantuan_pusat)}</TD>
                                <TD center mono>{fmtNum(row.asal_bantuan_prov)}</TD>
                                <TD center mono>{fmtNum(row.asal_bantuan_kab)}</TD>
                                <TD center mono>{fmtNum(row.asal_sumbangan)}</TD>
                                <TD center mono>{fmtNum(row.awal_baik)}</TD>
                                <TD center mono>{fmtNum(row.awal_rusak)}</TD>
                                <TD center mono>{fmtNum(row.hapus_rusak)}</TD>
                                <TD center mono>{fmtNum(row.hapus_dijual)}</TD>
                                <TD center mono>{fmtNum(row.hapus_disumbangkan)}</TD>
                                <TD center muted={row.tgl_penghapusan === '-'}>{row.tgl_penghapusan}</TD>
                                <TD center mono bold>{fmtNum(row.akhir_baik)}</TD>
                                <TD center mono bold>{fmtNum(row.akhir_rusak)}</TD>
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

export default function InventarisKekayaan({ auth, data, filters }) {
    const inventarisRows = Array.isArray(data) ? data : Object.values(data);
    const tahun = filters.tahun || new Date().getFullYear();

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="inventaris-kekayaan"
            judul="Buku Inventaris dan Kekayaan Desa"
            data={data}
            filters={filters}
            isCustomTable={true}
            isInventarisFilter={true}
        >
            <InventarisTable rows={inventarisRows} tahun={tahun} />
        </BukuLayout>
    );
}

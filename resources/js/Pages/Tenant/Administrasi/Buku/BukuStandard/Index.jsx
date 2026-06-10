import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

const formatRp = (v) => {
    const n = Number(v || 0);
    if (n === 0) return '—';
    return 'Rp ' + n.toLocaleString('id-ID');
};

const fmtDate = (d) => {
    if (!d) return '—';
    const date = new Date(d);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const JENIS_BUKU_META = {
    'buku-kas-umum': {
        judul: 'Buku Kas Umum',
        hasTahunFilter: true,
        hideDateFilter: true,
        columns: ['No.', 'Tgl', 'No. Bukti', 'Uraian Pengeluaran', 'Rekening', 'Jenis Bukti', 'Jumlah (Rp)', 'Status SPJ'],
        renderRow: (item, index, no) => (
            <tr key={item.id || index} className="border-b border-gray-50 hover:bg-green-50/30 transition-colors">
                <td className="px-3 py-3 text-center font-black text-gray-400 text-[10px]">{no + 1}</td>
                <td className="px-3 py-3 text-center text-[10px] font-bold text-gray-600 whitespace-nowrap">
                    {fmtDate(item.tanggal_pengeluaran)}
                </td>
                <td className="px-3 py-3 font-mono text-[9px] text-gray-500">{item.no_bukti || '—'}</td>
                <td className="px-3 py-3">
                    <p className="font-black text-[10px] text-gray-900 leading-tight">{item.nama_pengeluaran}</p>
                    {item.nama_penerima && (
                        <p className="text-[9px] text-gray-500 mt-0.5">Penerima: {item.nama_penerima}</p>
                    )}
                    {item.keterangan && (
                        <p className="text-[9px] text-gray-400 italic mt-0.5">{item.keterangan}</p>
                    )}
                </td>
                <td className="px-3 py-3 text-[9px] text-gray-500">
                    {item.apbdes
                        ? <><span className="font-black text-gray-700">[{item.apbdes.kode_rekening}]</span> {item.apbdes.nama_rekening}</>
                        : '—'
                    }
                </td>
                <td className="px-3 py-3 text-center">
                    <span className="inline-flex px-2 py-0.5 rounded-full text-[8px] font-black uppercase bg-green-50 text-green-700 border border-green-200">
                        {item.jenis_bukti || '—'}
                    </span>
                </td>
                <td className="px-3 py-3 text-right font-black text-[10px] text-gray-900 whitespace-nowrap">
                    {formatRp(item.jumlah)}
                </td>
                <td className="px-3 py-3 text-center">
                    <span className={`inline-flex px-2 py-0.5 rounded-full text-[8px] font-black uppercase ${
                        item.spj_status === 'sudah'
                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                            : 'bg-amber-50 text-amber-700 border border-amber-200'
                    }`}>
                        {item.spj_status === 'sudah' ? '✓ Sudah' : '⏳ Belum'}
                    </span>
                </td>
            </tr>
        ),
    },
    'kader-pemberdayaan': { judul: 'Buku Kader Pemberdayaan Masyarakat' },
    'buku-ekspedisi':     { judul: 'Buku Ekspedisi' },
    'buku-apb-desa':      { judul: 'Buku APB Desa', hasTahunFilter: true, hideDateFilter: true },
    'buku-rab':           { judul: 'Buku Rencana Anggaran Biaya', hasTahunFilter: true, hideDateFilter: true },
    'buku-kas-pembantu-pajak': { judul: 'Buku Kas Pembantu Pajak', hasTahunFilter: true, hideDateFilter: true },
    'buku-bank-desa':     { judul: 'Buku Bank Desa' },
};

export default function BukuStandard({ auth, jenis_buku, data, filters }) {
    const meta = JENIS_BUKU_META[jenis_buku] ?? { judul: 'Buku Administrasi' };
    const judul = meta.judul;

    // === Buku Kas Umum: dedicated table ===
    if (jenis_buku === 'buku-kas-umum' && meta.columns && meta.renderRow) {
        const tableHead = (
            <tr className="bg-green-700">
                {meta.columns.map(col => (
                    <th key={col} className="px-3 py-2.5 text-center text-[9px] font-black text-white uppercase tracking-widest border-r border-green-600 last:border-r-0">
                        {col}
                    </th>
                ))}
            </tr>
        );

        // Footer total
        const totalJumlah = data?.data?.reduce((s, i) => s + Number(i.jumlah || 0), 0) ?? 0;
        const footerRow = data?.data?.length > 0 ? (
            <tr className="bg-green-700 text-white">
                <td colSpan={6} className="px-3 py-2.5 text-right text-[9px] font-black uppercase tracking-widest">
                    Total Pengeluaran
                </td>
                <td className="px-3 py-2.5 text-right font-black text-[10px] whitespace-nowrap">
                    {formatRp(totalJumlah)}
                </td>
                <td></td>
            </tr>
        ) : null;

        const renderRow = (item, index) => meta.renderRow(item, index, (data?.from ?? 0) + index - 1);

        return (
            <BukuLayout
                auth={auth}
                jenis_buku={jenis_buku}
                judul={judul}
                data={data}
                filters={filters}
                tableHead={tableHead}
                renderRow={(item, index, no) => meta.renderRow(item, index, no)}
                hasStandardFilter={true}
                hasTahunFilter={true}
                hideDateFilter={true}
                isCustomTable={false}
            />
        );
    }

    // === Fallback generic placeholder ===
    const tableHead = (
        <tr className="bg-gray-50/80 border-b border-gray-100">
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest text-[10px]">
                Data belum tersedia — {judul}
            </th>
        </tr>
    );

    const renderRow = (item, index) => (
        <tr key={item.id || index} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold text-xs">
                {JSON.stringify(item).substring(0, 80)}…
            </td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku={jenis_buku}
            judul={judul}
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
            hasTahunFilter={meta.hasTahunFilter ?? false}
            hideDateFilter={meta.hideDateFilter ?? true}
        />
    );
}

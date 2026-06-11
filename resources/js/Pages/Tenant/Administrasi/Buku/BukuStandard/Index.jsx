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
        columns: ['No.', 'Tanggal', 'Kode Rekening', 'Uraian', 'No. Bukti', 'Penerimaan', 'Pengeluaran', 'Pengeluaran Kumulatif', 'Saldo'],
        renderRow: (item, index, no) => (
            <tr key={item.id || index} className="border-b border-gray-50 hover:bg-green-50/30 transition-colors">
                <td className="px-3 py-3 text-center font-black text-gray-400 text-[10px]">{no}</td>
                <td className="px-3 py-3 text-center text-[10px] font-bold text-gray-600 whitespace-nowrap">
                    {fmtDate(item.tanggal)}
                </td>
                <td className="px-3 py-3 font-mono text-[9px] text-gray-500 text-center">
                    {item.kode_rekening || '—'}
                </td>
                <td className="px-3 py-3 text-[10px] text-gray-900">
                    {item.uraian || '—'}
                    {item.nama_penerima && <div className="text-[9px] text-gray-500">Penerima: {item.nama_penerima}</div>}
                </td>
                <td className="px-3 py-3 font-mono text-[9px] text-gray-500 text-center">{item.no_bukti || '—'}</td>
                <td className="px-3 py-3 text-right font-black text-[10px] text-green-600 whitespace-nowrap">
                    {item.penerimaan > 0 ? formatRp(item.penerimaan) : '—'}
                </td>
                <td className="px-3 py-3 text-right font-black text-[10px] text-red-600 whitespace-nowrap">
                    {item.pengeluaran > 0 ? formatRp(item.pengeluaran) : '—'}
                </td>
                <td className="px-3 py-3 text-right font-black text-[10px] text-gray-700 whitespace-nowrap">
                    {formatRp(item.kumulatif_pengeluaran)}
                </td>
                <td className="px-3 py-3 text-right font-black text-[10px] text-blue-700 whitespace-nowrap">
                    {formatRp(item.saldo)}
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
        const totalPenerimaan = data?.data?.reduce((s, i) => s + Number(i.penerimaan || 0), 0) ?? 0;
        const totalPengeluaran = data?.data?.reduce((s, i) => s + Number(i.pengeluaran || 0), 0) ?? 0;
        const footerRow = data?.data?.length > 0 ? (
            <tr className="bg-green-700 text-white">
                <td colSpan={5} className="px-3 py-2.5 text-right text-[9px] font-black uppercase tracking-widest">
                    TOTAL
                </td>
                <td className="px-3 py-2.5 text-right font-black text-[10px] whitespace-nowrap">
                    {formatRp(totalPenerimaan)}
                </td>
                <td className="px-3 py-2.5 text-right font-black text-[10px] whitespace-nowrap">
                    {formatRp(totalPengeluaran)}
                </td>
                <td colSpan={2}></td>
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
                tableFooter={footerRow}
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

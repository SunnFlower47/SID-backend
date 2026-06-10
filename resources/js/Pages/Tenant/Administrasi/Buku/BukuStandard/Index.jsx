import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

// Digunakan sebagai fallback sementara untuk jenis buku yang belum punya view spesifik
export default function BukuStandard({ auth, jenis_buku, data, filters }) {
    const getJudulBuku = () => {
        switch (jenis_buku) {
            case 'kader-pemberdayaan': return 'Buku Kader Pemberdayaan Masyarakat';
            case 'buku-ekspedisi': return 'Buku Ekspedisi';
            case 'buku-apb-desa': return 'Buku Anggaran Pendapatan dan Belanja Desa';
            case 'buku-rab': return 'Buku Rencana Anggaran Biaya';
            case 'buku-kas-umum': return 'Buku Kas Umum';
            case 'buku-kas-pembantu-kegiatan': return 'Buku Kas Pembantu Kegiatan';
            case 'buku-kas-pembantu-pajak': return 'Buku Kas Pembantu Pajak';
            case 'buku-bank-desa': return 'Buku Bank Desa';
            default: return 'Buku Administrasi';
        }
    };

    const tableHead = (
        <tr className="bg-gray-50/80 border-b border-gray-100">
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Kolom Buku Standar</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold">Data akan ditampilkan segera.</td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku={jenis_buku}
            judul={getJudulBuku()}
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
            hideDateFilter={true}
        />
    );
}

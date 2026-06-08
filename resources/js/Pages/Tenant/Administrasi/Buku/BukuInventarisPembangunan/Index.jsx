import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function BukuInventarisPembangunan({ auth, data, filters }) {
    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA PROYEK / HASIL PEMBANGUNAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">VOLUME</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-right">BIAYA (Rp)</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">LOKASI</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KETERANGAN</th>
            </tr>
            <tr className="bg-gray-100 text-gray-500 font-bold uppercase text-[9px] tracking-wider border-b border-gray-200 whitespace-nowrap">
            </tr>
            <tr className="bg-gray-100 text-gray-500 font-bold uppercase text-[9px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                {Array.from({ length: 6 }).map((_, i) => (
                    <th key={i} className="px-4 py-1 border-r border-gray-200 text-center">{i + 1}</th>
                ))}
            </tr>
        </>
    );

    const renderRow = (item, index, no) => {
        return (
            <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs border-r">{no}</td>
                <td className="px-4 py-3 font-bold text-gray-900 border-r">{item.nama_proyek}</td>
                <td className="px-4 py-3 text-center border-r">{item.volume || '-'}</td>
                <td className="px-4 py-3 text-right font-mono text-xs border-r font-bold">{formatRupiah(item.anggaran)}</td>
                <td className="px-4 py-3 text-center border-r">{item.lokasi || '-'}</td>
                <td className="px-4 py-3 max-w-[200px] truncate border-r" title={item.catatan}>{item.catatan || '-'}</td>
            </tr>
        );
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-inventaris-pembangunan"
            judul="Buku Inventaris Hasil Pembangunan"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
            hasTahunFilter={true}
            hideDateFilter={true}
        />
    );
}

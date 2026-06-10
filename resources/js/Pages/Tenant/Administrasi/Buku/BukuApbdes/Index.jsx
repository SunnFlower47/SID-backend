import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function BukuApbdes({ auth, data, filters }) {
    const tableHead = (
        <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-center w-16">NO</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-center w-32">KODE REKENING</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle">URAIAN</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-right">ANGGARAN (Rp)</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-right">KETERANGAN</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
            <td className="px-4 py-3 text-center font-mono text-xs text-gray-500">{no}</td>
            <td className="px-4 py-3 text-center font-mono text-xs font-bold text-gray-700">{item.kode_rekening || '-'}</td>
            <td className="px-4 py-3 font-bold text-gray-900">{item.nama_rekening || '-'}</td>
            <td className="px-4 py-3 text-right font-mono font-bold text-green-700">
                {new Intl.NumberFormat('id-ID').format(item.anggaran || 0)}
            </td>
            <td className="px-4 py-3 text-right text-gray-600">{item.keterangan || '-'}</td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-apb-desa"
            judul="Buku APB Desa"
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

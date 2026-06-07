import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function TanahDiDesa({ auth, data, filters }) {
    const tableHead = (
        <tr className="bg-gray-50/80 border-b border-gray-100">
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama Pemilik</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Luas (m²)</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Rincian Hak & Penggunaan</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Keterangan</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
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

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="tanah-di-desa"
            judul="Buku Tanah di Desa"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
        />
    );
}

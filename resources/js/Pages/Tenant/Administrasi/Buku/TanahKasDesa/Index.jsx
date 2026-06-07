import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function TanahKasDesa({ auth, data, filters }) {
    const tableHead = (
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

    const renderRow = (item, index, no) => {
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
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="tanah-kas-desa"
            judul="Buku Tanah Kas Desa"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
        />
    );
}

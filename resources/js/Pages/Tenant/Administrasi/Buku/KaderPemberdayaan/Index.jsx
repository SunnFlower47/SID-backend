import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function KaderPemberdayaan({ auth, data, filters }) {
    const tableHead = (
        <tr className="bg-gray-50/80 border-b border-gray-100">
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Umur</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">L/P</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Pendidikan</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Bidang</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Alamat</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Keterangan</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
            <td className="px-4 py-3 font-bold text-gray-900">{item.nama}</td>
            <td className="px-4 py-3 text-center text-gray-600">{item.umur}</td>
            <td className="px-4 py-3 text-center font-bold text-gray-800">{item.jenis_kelamin === 'L' || item.jenis_kelamin === 'Laki-laki' ? 'L' : 'P'}</td>
            <td className="px-4 py-3 text-center text-gray-600">{item.pendidikan_terakhir || '-'}</td>
            <td className="px-4 py-3 text-gray-800 font-medium">{item.bidang || '-'}</td>
            <td className="px-4 py-3 text-gray-600">{item.alamat || '-'}</td>
            <td className="px-4 py-3 text-gray-600">{item.keterangan || '-'}</td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="kader-pemberdayaan"
            judul="Buku Kader Pemberdayaan Masyarakat"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
            hideDateFilter={true}
        />
    );
}

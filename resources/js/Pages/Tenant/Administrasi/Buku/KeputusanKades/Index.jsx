import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import { Badge } from '@/Components/Shared';

export default function KeputusanKades({ auth, data, filters }) {
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

    const tableHead = (
        <tr className="bg-gray-50/80 border-b border-gray-100">
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-12">No</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest w-48">Nomor Keputusan</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Tentang / Uraian Singkat</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-32">Tgl Ditetapkan</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Keterangan</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
            <td className="px-4 py-3 font-mono font-bold text-gray-900">{item.nomor_keputusan}</td>
            <td className="px-4 py-3 text-gray-800">{item.judul_keputusan}</td>
            <td className="px-4 py-3 text-center"><Badge color="blue">{formatDate(item.tanggal_ditetapkan)}</Badge></td>
            <td className="px-4 py-3 text-gray-600">{item.keterangan || '-'}</td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="keputusan-kades"
            judul="Buku Keputusan Kepala Desa"
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

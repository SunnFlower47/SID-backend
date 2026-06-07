import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import { Badge } from '@/Components/Shared';

export default function PeraturanDesa({ auth, data, filters }) {
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
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Jenis Peraturan</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nomor &amp; Tanggal</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Tentang</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Status</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
            <td className="px-4 py-3 font-bold text-gray-900">{item.jenis_peraturan}</td>
            <td className="px-4 py-3">
                <div className="font-mono text-gray-900">{item.nomor_peraturan || '-'}</div>
                <div className="text-xs text-gray-500">{formatDate(item.tanggal_ditetapkan)}</div>
            </td>
            <td className="px-4 py-3 text-gray-800">{item.judul}</td>
            <td className="px-4 py-3 text-center">
                <Badge color={item.status === 'disetujui' ? 'green' : 'amber'}>{item.status}</Badge>
            </td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="peraturan-desa"
            judul="Buku Peraturan di Desa"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
        />
    );
}

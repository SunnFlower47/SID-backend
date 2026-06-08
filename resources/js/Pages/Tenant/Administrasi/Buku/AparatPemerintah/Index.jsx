import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import { Badge } from '@/Components/Shared';

export default function AparatPemerintah({ auth, data, filters }) {
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
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama &amp; NIP</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Pangkat/Jabatan</th>
            <th className="px-4 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Tanggal SK</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Status</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
            <td className="px-4 py-3">
                <div className="font-bold text-gray-900">{item.nama}</div>
                <div className="font-mono text-xs text-gray-500">{item.nip || '-'}</div>
            </td>
            <td className="px-4 py-3 text-gray-800 uppercase text-xs font-bold">{item.jabatan}</td>
            <td className="px-4 py-3 text-center">{formatDate(item.tanggal_pengangkatan)}</td>
            <td className="px-4 py-3">
                <Badge color={item.status_aktif ? 'green' : 'red'}>{item.status_aktif ? 'Aktif' : 'Non-Aktif'}</Badge>
            </td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="aparat-pemerintah"
            judul="Buku Aparat Pemerintah Desa"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
            hideDateFilter={true}
        />
    );
}

import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import { Badge } from '@/Components/Shared';

export default function BukuAgenda({ auth, data, filters }) {
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
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Tanggal Catat</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Informasi Surat</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Pengirim / Penerima</th>
            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Isi Singkat</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
            <td className="px-4 py-3 text-center text-gray-500 font-bold">{no}</td>
            <td className="px-4 py-3">
                <div className="font-bold text-gray-900">{formatDate(item.tanggal)}</div>
                <div className="mt-1">
                    {item.jenis_surat === 'Masuk' ? (
                        <Badge color="green">Surat Masuk</Badge>
                    ) : (
                        <Badge color="blue">Surat Keluar</Badge>
                    )}
                </div>
            </td>
            <td className="px-4 py-3">
                <div className="font-bold text-gray-900">{item.nomor_surat || '-'}</div>
                <div className="text-xs text-gray-500">Tgl: {formatDate(item.tanggal_surat)}</div>
            </td>
            <td className="px-4 py-3">
                <div className="text-gray-900 font-medium truncate max-w-xs">{item.pengirim_penerima}</div>
                {item.keterangan && <div className="text-xs text-gray-500 truncate max-w-xs">{item.keterangan}</div>}
            </td>
            <td className="px-4 py-3">
                <div className="text-gray-700 max-w-xs line-clamp-2">{item.isi_singkat}</div>
            </td>
        </tr>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-agenda"
            judul="Buku Agenda"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
        />
    );
}

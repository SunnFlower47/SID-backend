import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function BukuKegiatanPembangunan({ auth, data, filters }) {
    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA PROYEK / KEGIATAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">VOLUME</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">SUMBER DANA</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-right">JUMLAH</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">WAKTU</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">SIFAT PROYEK</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">PELAKSANA</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KET</th>
            </tr>
            <tr className="bg-gray-100 text-gray-500 font-bold uppercase text-[9px] tracking-wider border-b border-gray-200 whitespace-nowrap">
            </tr>
            <tr className="bg-gray-100 text-gray-500 font-bold uppercase text-[9px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                {Array.from({ length: 9 }).map((_, i) => (
                    <th key={i} className="px-4 py-1 border-r border-gray-200 text-center">{i + 1}</th>
                ))}
            </tr>
        </>
    );

    const renderRow = (item, index, no) => {
        let waktu = '-';
        if (item.tanggal_mulai) {
            const dateStr = new Date(item.tanggal_mulai).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
            const dateEnd = item.tanggal_selesai ? new Date(item.tanggal_selesai).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '?';
            waktu = `${dateStr} s/d ${dateEnd}`;
        }
        
        return (
            <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs border-r">{no}</td>
                <td className="px-4 py-3 font-bold text-gray-900 border-r">{item.nama_proyek}</td>
                <td className="px-4 py-3 text-center border-r">{item.volume || '-'}</td>
                <td className="px-4 py-3 text-center border-r">{item.apbdes?.sumber_dana || '-'}</td>
                <td className="px-4 py-3 text-right font-mono text-xs border-r font-bold">{formatRupiah(item.anggaran)}</td>
                <td className="px-4 py-3 text-center border-r">{waktu}</td>
                <td className="px-4 py-3 text-center border-r font-bold uppercase text-[10px]">{item.sifat_proyek || 'BARU'}</td>
                <td className="px-4 py-3 text-center border-r">{item.penanggung_jawab || '-'}</td>
                <td className="px-4 py-3 max-w-[100px] truncate border-r" title={item.catatan || item.status}>{item.catatan || item.status}</td>
            </tr>
        );
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-kegiatan-pembangunan"
            judul="Buku Kegiatan Pembangunan"
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

import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function BukuPendudukSementara({ auth, data, filters }) {
    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">NAMA LENGKAP</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle text-center">JENIS<br/>KELAMIN</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">TEMPAT DAN<br/>TGL LAHIR</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">PEKERJAAN</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">KEWARGA-<br/>NEGARAAN</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">DATANG DARI</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">MAKSUD DAN<br/>TUJUAN DATANG</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">NAMA DAN ALAMAT<br/>YANG DIDATANGI</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle text-center">DATANG<br/>TANGGAL</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle text-center">PERGI<br/>TANGGAL</th>
                <th className="px-4 py-3 border-r border-gray-200 align-middle">KETERANGAN</th>
            </tr>
            <tr className="bg-gray-100 text-gray-500 font-bold uppercase text-[9px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                {Array.from({ length: 12 }).map((_, i) => (
                    <th key={i} className="px-4 py-1 border-r border-gray-200 text-center">{i + 1}</th>
                ))}
            </tr>
        </>
    );

    const renderRow = (item, index, no) => {
        let ttl = '-';
        if (item.tempat_lahir || item.tanggal_lahir) {
            const dateStr = item.tanggal_lahir ? new Date(item.tanggal_lahir).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '';
            ttl = `${item.tempat_lahir || ''}, ${dateStr}`;
        }
        
        const datangTgl = item.tanggal_masuk ? new Date(item.tanggal_masuk).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '-';
        const pergiTgl = item.tanggal_berlaku ? new Date(item.tanggal_berlaku).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '-';
        const jk = (item.jenis_kelamin === 'Laki-Laki' || item.jenis_kelamin === 'LAKI-LAKI' || item.jenis_kelamin === 'L') ? 'L' : 'P';

        return (
            <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs border-r">{no}</td>
                <td className="px-4 py-3 font-bold text-gray-900 border-r">{item.nama}</td>
                <td className="px-4 py-3 text-center border-r font-bold">{jk}</td>
                <td className="px-4 py-3 border-r">{ttl}</td>
                <td className="px-4 py-3 border-r">{item.pekerjaan || '-'}</td>
                <td className="px-4 py-3 text-center border-r">WNI</td>
                <td className="px-4 py-3 border-r max-w-[150px] truncate" title={item.asal_daerah || item.alamat_asal}>{item.asal_daerah || item.alamat_asal || '-'}</td>
                <td className="px-4 py-3 border-r max-w-[150px] truncate" title={item.keperluan_domisili}>{item.keperluan_domisili || '-'}</td>
                <td className="px-4 py-3 border-r max-w-[150px] truncate" title={item.alamat_tinggal}>{item.alamat_tinggal || '-'}</td>
                <td className="px-4 py-3 text-center border-r">{datangTgl}</td>
                <td className="px-4 py-3 text-center border-r">{pergiTgl}</td>
                <td className="px-4 py-3 max-w-[100px] truncate" title={item.catatan}>{item.catatan || '-'}</td>
            </tr>
        );
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-penduduk-sementara"
            judul="Buku Penduduk Sementara"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
        />
    );
}

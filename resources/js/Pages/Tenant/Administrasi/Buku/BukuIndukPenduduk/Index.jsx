import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import ResidentFilters from '@/Components/Penduduk/ResidentFilters';
import { Badge } from '@/Components/Shared';

export default function BukuIndukPenduduk({ auth, data, filters, rtList, rwList, dusunList }) {
    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NOMOR URUT</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA LENGKAP / PANGGILAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">JENIS KELAMIN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">STATUS PERKAWINAN</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">TEMPAT & TANGGAL LAHIR</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">AGAMA</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">PENDIDIKAN TERAKHIR</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">PEKERJAAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">DAPAT MEMBACA HURUF</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">KEWARGANEGARAAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">ALAMAT LENGKAP</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KEDUDUKAN DLM KELUARGA</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NIK</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NO. KK</th>
            </tr>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th className="px-4 py-2 border-r border-gray-200 text-center">TEMPAT LAHIR</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">TGL</th>
            </tr>
        </>
    );

    const renderRow = (item, index, no) => (
        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
            <td className="px-4 py-3 text-center font-mono text-xs">{no}</td>
            <td className="px-4 py-3 font-bold text-gray-900">{item.nama}</td>
            <td className="px-4 py-3 text-center font-bold">{item.jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P'}</td>
            <td className="px-4 py-3">{item.status_perkawinan || '-'}</td>
            <td className="px-4 py-3">{item.tempat_lahir || '-'}</td>
            <td className="px-4 py-3 text-center">{item.tanggal_lahir ? item.tanggal_lahir.split('T')[0].split('-').reverse().join('-') : '-'}</td>
            <td className="px-4 py-3">{item.agama || '-'}</td>
            <td className="px-4 py-3">{item.pendidikan || '-'}</td>
            <td className="px-4 py-3">{item.pekerjaan || '-'}</td>
            <td className="px-4 py-3 text-center">{item.dapat_membaca_huruf || '-'}</td>
            <td className="px-4 py-3 text-center">{item.warganegara || item.kewarganegaraan || 'WNI'}</td>
            <td className="px-4 py-3 text-xs max-w-[200px] truncate" title={item.alamat}>
                {item.alamat || '-'}
            </td>
            <td className="px-4 py-3">{item.kedudukan_keluarga || '-'}</td>
            <td className="px-4 py-3 font-mono text-xs">{item.nik || '-'}</td>
            <td className="px-4 py-3 font-mono text-xs text-green-700 font-bold bg-green-50/50 rounded px-1">{item.nkk || item.kartu_keluarga?.nkk || '-'}</td>
        </tr>
    );

    const customFilter = (
        <ResidentFilters 
            filters={filters} 
            rtList={rtList || []} 
            rwList={rwList || []} 
            dusunList={dusunList || []} 
            submitRouteName="administrasi.buku.show"
            submitRouteParams={{ jenis_buku: 'buku-induk-penduduk' }}
        />
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-induk-penduduk"
            judul="Buku Induk Penduduk"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            customFilter={customFilter}
            hasStandardFilter={true}
            hideDateFilter={true}
        />
    );
}

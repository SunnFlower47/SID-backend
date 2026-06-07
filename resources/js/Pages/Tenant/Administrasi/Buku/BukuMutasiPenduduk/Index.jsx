import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function BukuMutasiPenduduk({ auth, data, filters }) {
    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA LENGKAP</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">TEMPAT DAN<br/>TGL LAHIR</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">JENIS<br/>KELAMIN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KEWARGA-<br/>NEGARAAN</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">DATANG DARI</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">PINDAH KE</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">MENINGGAL<br/>(TEMPAT & TGL)</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KETERANGAN</th>
            </tr>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th className="px-4 py-2 border-r border-gray-200 text-center">ASAL USUL</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">TANGGAL</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">TUJUAN</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">TANGGAL</th>
            </tr>
        </>
    );

    const renderRow = (item, index, no) => {
        const penduduk = item.penduduk;
        
        let ttl = '-';
        if (penduduk) {
            const dateStr = penduduk.tanggal_lahir ? new Date(penduduk.tanggal_lahir).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '';
            ttl = `${penduduk.tempat_lahir || ''}, ${dateStr}`;
        }
        
        let datangDari = '-';
        let tglDatang = '-';
        let pindahKe = '-';
        let tglPindah = '-';
        let meninggal = '-';
        
        const tglMutasi = item.tanggal_mutasi ? new Date(item.tanggal_mutasi).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '-';

        if (item.jenis_mutasi === 'pindah_masuk' || item.jenis_mutasi === 'kelahiran') {
            datangDari = item.asal_tujuan || '-';
            tglDatang = tglMutasi;
        } else if (item.jenis_mutasi === 'pindah_keluar') {
            pindahKe = item.asal_tujuan || '-';
            tglPindah = tglMutasi;
        } else if (item.jenis_mutasi === 'kematian') {
            const lokasi = item.asal_tujuan || 'Tidak diketahui';
            meninggal = `${tglMutasi} di ${lokasi}`;
        }

        return (
            <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs">{no}</td>
                <td className="px-4 py-3 font-bold text-gray-900">{penduduk ? penduduk.nama : 'Penduduk Terhapus'}</td>
                <td className="px-4 py-3">{ttl}</td>
                <td className="px-4 py-3 text-center font-bold">{penduduk ? (penduduk.jenis_kelamin === 'Laki-Laki' || penduduk.jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P') : '-'}</td>
                <td className="px-4 py-3 text-center">{penduduk ? (penduduk.warganegara || penduduk.kewarganegaraan || 'WNI') : '-'}</td>
                <td className="px-4 py-3">{datangDari}</td>
                <td className="px-4 py-3 text-center">{tglDatang}</td>
                <td className="px-4 py-3">{pindahKe}</td>
                <td className="px-4 py-3 text-center">{tglPindah}</td>
                <td className="px-4 py-3">{meninggal}</td>
                <td className="px-4 py-3">{item.alasan || '-'}</td>
            </tr>
        );
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-mutasi-penduduk"
            judul="Buku Mutasi Penduduk"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
        />
    );
}

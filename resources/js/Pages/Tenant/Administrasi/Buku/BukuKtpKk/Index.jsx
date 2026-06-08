import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function BukuKtpKk({ auth, data, filters }) {
    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO. KK</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA LENGKAP</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NIK</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">JENIS<br/>KELAMIN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">TEMPAT /<br/>TGL LAHIR</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">GOL<br/>DARAH</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">AGAMA</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">PENDIDIKAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">PEKERJAAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">ALAMAT</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">STATUS<br/>PERKAWINAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">TEMPAT/TGL<br/>DIKELUARKAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">STATUS<br/>HUBUNGAN</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">KEWARGA-<br/>NEGARAAN</th>
                <th colSpan="2" className="px-4 py-2 border-r border-gray-200 align-middle text-center border-b">ORANG TUA</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">TGL MULAI<br/>TINGGAL</th>
                <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KET</th>
            </tr>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th className="px-4 py-2 border-r border-gray-200 align-middle text-center">AYAH</th>
                <th className="px-4 py-2 border-r border-gray-200 align-middle text-center">IBU</th>
            </tr>
            <tr className="bg-gray-100 text-gray-500 font-bold uppercase text-[9px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                {Array.from({ length: 19 }).map((_, i) => (
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
        
        const jk = (item.jenis_kelamin === 'Laki-Laki' || item.jenis_kelamin === 'LAKI-LAKI' || item.jenis_kelamin === 'L') ? 'L' : 'P';
        const tanggalMasuk = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '-';

        let tanggalDikeluarkan = '-';
        if (item.kartu_keluarga && (item.kartu_keluarga.tempat_dikeluarkan || item.kartu_keluarga.tanggal_dikeluarkan)) {
            const dateDik = item.kartu_keluarga.tanggal_dikeluarkan ? new Date(item.kartu_keluarga.tanggal_dikeluarkan).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '';
            tanggalDikeluarkan = `${item.kartu_keluarga.tempat_dikeluarkan || ''}, ${dateDik}`;
        }

        return (
            <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs border-r">{no}</td>
                <td className="px-4 py-3 text-center border-r font-mono text-xs">{item.kartu_keluarga ? item.kartu_keluarga.nkk : '-'}</td>
                <td className="px-4 py-3 font-bold text-gray-900 border-r">{item.nama}</td>
                <td className="px-4 py-3 text-center border-r font-mono text-xs">{item.nik}</td>
                <td className="px-4 py-3 text-center border-r font-bold">{jk}</td>
                <td className="px-4 py-3 border-r">{ttl}</td>
                <td className="px-4 py-3 text-center border-r">{item.golongan_darah || '-'}</td>
                <td className="px-4 py-3 border-r">{item.agama || '-'}</td>
                <td className="px-4 py-3 border-r">{item.pendidikan || '-'}</td>
                <td className="px-4 py-3 border-r">{item.pekerjaan || '-'}</td>
                <td className="px-4 py-3 border-r max-w-[150px] truncate" title={item.kartu_keluarga?.alamat}>{item.kartu_keluarga?.alamat || '-'}</td>
                <td className="px-4 py-3 border-r">{item.status_perkawinan || '-'}</td>
                <td className="px-4 py-3 text-center border-r">{tanggalDikeluarkan}</td>
                <td className="px-4 py-3 border-r">{item.kedudukan_keluarga || '-'}</td>
                <td className="px-4 py-3 text-center border-r">{item.kewarganegaraan || item.warganegara || 'WNI'}</td>
                <td className="px-4 py-3 border-r">{item.nama_ayah || '-'}</td>
                <td className="px-4 py-3 border-r">{item.nama_ibu || '-'}</td>
                <td className="px-4 py-3 text-center border-r">{tanggalMasuk}</td>
                <td className="px-4 py-3 max-w-[100px] truncate" title={item.keterangan}>{item.keterangan || '-'}</td>
            </tr>
        );
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-ktp-kk"
            judul="Buku KTP dan KK"
            data={data}
            filters={filters}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={true}
            hideDateFilter={true}
        />
    );
}

import React from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import { Fragment } from 'react';

export default function BukuRab({ auth, data, filters }) {
    const tableHead = (
        <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-center w-32">KODE REKENING</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle">URAIAN</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-center">VOLUME</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-right">HARGA SATUAN</th>
            <th className="px-4 py-3 border-r border-gray-200 align-middle text-right">JUMLAH (Rp)</th>
        </tr>
    );

    const renderRow = (item, index, no) => (
        <Fragment key={item.id}>
            {/* Header Rekening */}
            <tr className="border-b border-gray-200 bg-gray-50/50 hover:bg-gray-100/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs font-black text-gray-800 border-r border-gray-100">{item.kode_rekening || '-'}</td>
                <td className="px-4 py-3 font-black text-gray-900 border-r border-gray-100">{item.nama_rekening || '-'}</td>
                <td className="px-4 py-3 border-r border-gray-100"></td>
                <td className="px-4 py-3 border-r border-gray-100"></td>
                <td className="px-4 py-3 text-right font-mono font-black text-blue-800">
                    {new Intl.NumberFormat('id-ID').format(item.anggaran || 0)}
                </td>
            </tr>
            {/* Rincian Rekening */}
            {item.rincians?.length > 0 ? (
                item.rincians.map((rincian) => (
                    <tr key={rincian.id} className="border-b border-gray-50 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        <td className="px-4 py-2 border-r border-gray-100"></td>
                        <td className="px-4 py-2 border-r border-gray-100 pl-8">
                            <span className="text-gray-400 mr-2">-</span>
                            <span className="font-bold text-gray-700">{rincian.uraian}</span>
                        </td>
                        <td className="px-4 py-2 text-center text-xs font-mono text-gray-600 border-r border-gray-100">
                            {rincian.volume} {rincian.satuan}
                        </td>
                        <td className="px-4 py-2 text-right font-mono text-xs text-gray-600 border-r border-gray-100">
                            {new Intl.NumberFormat('id-ID').format(rincian.harga_satuan || 0)}
                        </td>
                        <td className="px-4 py-2 text-right font-mono text-xs font-bold text-green-700">
                            {new Intl.NumberFormat('id-ID').format(rincian.jumlah || 0)}
                        </td>
                    </tr>
                ))
            ) : (
                <tr className="border-b border-gray-50">
                    <td className="px-4 py-2 border-r border-gray-100"></td>
                    <td colSpan="4" className="px-4 py-2 text-xs italic text-gray-400 text-center">
                        Tidak ada rincian RAB
                    </td>
                </tr>
            )}
        </Fragment>
    );

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-rab"
            judul="Buku Rencana Anggaran Biaya (RAB)"
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

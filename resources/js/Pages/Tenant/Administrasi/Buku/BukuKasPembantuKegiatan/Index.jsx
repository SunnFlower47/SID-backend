import React, { useState } from 'react';
import { usePage, router } from '@inertiajs/react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';

export default function Index({ auth, jenis_buku, data, filters, apbdes_list }) {
    // Component is essentially a table wrapped in BukuLayout
    // C.3 specific filter: apbdes_id
    const [selectedApbdesId, setSelectedApbdesId] = useState(filters.apbdes_id || '');

    const handleFilterChange = (key, value) => {
        router.get(
            route('administrasi.buku.show', jenis_buku),
            { ...filters, [key]: value },
            { preserveState: true, replace: true }
        );
    };

    const handleApbdesChange = (e) => {
        setSelectedApbdesId(e.target.value);
    };

    const handleCustomFilterSubmit = (params, action) => {
        if (action === 'reset') {
            setSelectedApbdesId('');
            delete params.apbdes_id;
        } else {
            params.apbdes_id = selectedApbdesId;
        }
        return params;
    };

    const customFilter = (
        <div className="w-full lg:w-48 space-y-2 text-left">
            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                Pilih Rekening Belanja
            </label>
            <div className="relative">
                <select 
                    value={selectedApbdesId}
                    onChange={handleApbdesChange}
                    className="w-full py-3 px-4 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                >
                    <option value="">-- Pilih Rekening Belanja --</option>
                    {apbdes_list.map(kegiatan => (
                        <option key={kegiatan.id} value={kegiatan.id}>
                            {kegiatan.kode_rekening} - {kegiatan.nama_rekening}
                        </option>
                    ))}
                </select>
            </div>
        </div>
    );

    return (
        <BukuLayout 
            auth={auth} 
            judul="Buku Kas Pembantu Kegiatan" 
            desc="Mencatat riwayat penerimaan panjar dan pengeluaran belanja per kegiatan." 
            jenis_buku={jenis_buku}
            hideDateFilter={true}
            hasTahunFilter={true}
            extraFilterFields={customFilter}
            onCustomFilterSubmit={handleCustomFilterSubmit}
            isCustomTable={true}
        >
            {/* Table wrapper */}
            <div className="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                            <tr>
                                <th className="px-4 py-3 text-center w-12">NO</th>
                                <th className="px-4 py-3 whitespace-nowrap">TANGGAL</th>
                                <th className="px-4 py-3 min-w-[200px]">URAIAN</th>
                                <th className="px-4 py-3 text-right whitespace-nowrap">PENERIMAAN (Rp)</th>
                                <th className="px-4 py-3 text-right whitespace-nowrap">PENGELUARAN (Rp)</th>
                                <th className="px-4 py-3 whitespace-nowrap text-center">NO. BUKTI</th>
                                <th className="px-4 py-3 text-right whitespace-nowrap">SALDO (Rp)</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {data.data.length > 0 ? (
                                data.data.map((item, index) => (
                                    <tr key={item.id} className="hover:bg-gray-50/50 transition-colors">
                                        <td className="px-4 py-3 text-center text-gray-500">
                                            {data.from + index}
                                        </td>
                                        <td className="px-4 py-3 text-gray-900 whitespace-nowrap">
                                            {new Date(item.tanggal_pengeluaran).toLocaleDateString('id-ID', {
                                                day: '2-digit', month: 'short', year: 'numeric'
                                            })}
                                        </td>
                                        <td className="px-4 py-3 text-gray-700">
                                            {item.nama_pengeluaran}
                                        </td>
                                        <td className="px-4 py-3 text-right text-green-600 font-medium">
                                            {item.penerimaan > 0 ? new Intl.NumberFormat('id-ID').format(item.penerimaan) : '-'}
                                        </td>
                                        <td className="px-4 py-3 text-right text-red-600 font-medium">
                                            {item.pengeluaran > 0 ? new Intl.NumberFormat('id-ID').format(item.pengeluaran) : '-'}
                                        </td>
                                        <td className="px-4 py-3 text-center text-gray-500 font-mono text-xs">
                                            {item.no_bukti || '-'}
                                        </td>
                                        <td className="px-4 py-3 text-right text-blue-600 font-bold">
                                            {new Intl.NumberFormat('id-ID').format(item.saldo)}
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="px-4 py-8 text-center text-gray-500 bg-gray-50/30">
                                        {selectedApbdesId 
                                            ? "Belum ada transaksi (penerimaan/pengeluaran) untuk kegiatan ini." 
                                            : "Silakan pilih kegiatan pada filter di atas terlebih dahulu."}
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
            
            {/* Pagination Component if needed */}
            {data.links && data.links.length > 3 && (
                <div className="mt-4 flex justify-center">
                    <div className="flex gap-1">
                        {data.links.map((link, idx) => (
                            <button
                                key={idx}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                disabled={!link.url}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                className={`px-3 py-1 text-sm border rounded-lg transition-colors ${
                                    link.active 
                                        ? 'bg-blue-600 text-white border-blue-600' 
                                        : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                                } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                            />
                        ))}
                    </div>
                </div>
            )}
        </BukuLayout>
    );
}

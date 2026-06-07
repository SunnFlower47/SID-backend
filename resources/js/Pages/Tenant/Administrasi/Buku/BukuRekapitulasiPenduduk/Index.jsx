import React, { useState } from 'react';
import BukuLayout from '@/Components/Administrasi/BukuLayout';
import { Calendar, Search } from 'lucide-react';
import { router } from '@inertiajs/react';

export default function BukuRekapitulasiPenduduk({ auth, data, filters }) {
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;

    const [bulan, setBulan] = useState(filters?.bulan || String(currentMonth));
    const [tahun, setTahun] = useState(filters?.tahun || String(currentYear));

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(route('administrasi.buku.show', 'buku-rekapitulasi-penduduk'), { bulan, tahun }, { preserveState: true });
    };

    const handleReset = () => {
        setBulan(String(currentMonth));
        setTahun(String(currentYear));
        router.get(route('administrasi.buku.show', 'buku-rekapitulasi-penduduk'));
    };

    const isFiltered = bulan !== String(currentMonth) || tahun !== String(currentYear);

    const customFilter = (
        <form onSubmit={handleFilter} className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-end mb-6">
            <div className="w-full sm:w-48 space-y-2 text-left">
                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Bulan</label>
                <div className="relative">
                    <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <select
                        className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner appearance-none"
                        value={bulan}
                        onChange={(e) => setBulan(e.target.value)}
                    >
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
            </div>
            <div className="w-full sm:w-48 space-y-2 text-left">
                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tahun</label>
                <div className="relative">
                    <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        type="number"
                        placeholder="Masukkan Tahun..."
                        className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                        value={tahun}
                        onChange={(e) => setTahun(e.target.value)}
                        min="1900"
                        max="2099"
                    />
                </div>
            </div>
            <div className="flex gap-2 w-full sm:w-auto">
                <button type="submit" className="flex items-center justify-center gap-2 flex-1 sm:flex-none px-8 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black hover:bg-green-700 active:scale-95 transition-all uppercase tracking-widest shadow-md shadow-green-200">
                    <Search className="w-3.5 h-3.5" /> REKAP DATA
                </button>
                {isFiltered && (
                    <button type="button" onClick={handleReset} className="flex-1 sm:flex-none px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl text-[10px] font-black hover:bg-gray-200 transition-all uppercase tracking-widest">
                        RESET
                    </button>
                )}
            </div>
        </form>
    );

    const tableHead = (
        <>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th rowSpan="3" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NO</th>
                <th rowSpan="3" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA DUSUN / LINGKUNGAN / KEL</th>
                <th colSpan="5" className="px-4 py-2 border-r border-b border-gray-200 text-center">JUMLAH PENDUDUK AWAL BULAN</th>
                <th colSpan="4" className="px-4 py-2 border-r border-b border-gray-200 text-center">TAMBAHAN BULAN INI</th>
                <th colSpan="4" className="px-4 py-2 border-r border-b border-gray-200 text-center">PENGURANGAN BULAN INI</th>
                <th colSpan="5" className="px-4 py-2 border-r border-b border-gray-200 text-center">JUMLAH PENDUDUK AKHIR BULAN</th>
                <th rowSpan="3" className="px-4 py-3 border-gray-200 align-middle">KET</th>
            </tr>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">WNA</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">WNI</th>
                <th rowSpan="2" className="px-4 py-2 border-r border-gray-200 text-center align-middle">JML</th>
                
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">LAHIR</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">DATANG</th>
                
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">MATI</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">PINDAH</th>
                
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">WNA</th>
                <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">WNI</th>
                <th rowSpan="2" className="px-4 py-2 border-r border-gray-200 text-center align-middle">JML</th>
            </tr>
            <tr className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">L</th>
                <th className="px-4 py-2 border-r border-gray-200 text-center">P</th>
            </tr>
        </>
    );

    const renderRow = (item, index, no) => {
        const awalJml = item.awal_wna_l + item.awal_wna_p + item.awal_wni_l + item.awal_wni_p;
        const akhirJml = item.akhir_wna_l + item.akhir_wna_p + item.akhir_wni_l + item.akhir_wni_p;

        return (
            <tr key={item.dusun_id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors whitespace-nowrap">
                <td className="px-4 py-3 text-center font-mono text-xs border-r">{no}</td>
                <td className="px-4 py-3 font-bold text-gray-900 border-r">{item.nama_dusun.toUpperCase()}</td>
                
                <td className="px-4 py-3 text-center border-r">{item.awal_wna_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.awal_wna_p}</td>
                <td className="px-4 py-3 text-center border-r">{item.awal_wni_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.awal_wni_p}</td>
                <td className="px-4 py-3 text-center border-r font-bold bg-blue-50/50">{awalJml}</td>
                
                <td className="px-4 py-3 text-center border-r">{item.tambah_lahir_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.tambah_lahir_p}</td>
                <td className="px-4 py-3 text-center border-r">{item.tambah_datang_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.tambah_datang_p}</td>
                
                <td className="px-4 py-3 text-center border-r">{item.kurang_mati_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.kurang_mati_p}</td>
                <td className="px-4 py-3 text-center border-r">{item.kurang_pindah_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.kurang_pindah_p}</td>
                
                <td className="px-4 py-3 text-center border-r">{item.akhir_wna_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.akhir_wna_p}</td>
                <td className="px-4 py-3 text-center border-r">{item.akhir_wni_l}</td>
                <td className="px-4 py-3 text-center border-r">{item.akhir_wni_p}</td>
                <td className="px-4 py-3 text-center border-r font-bold bg-green-50/50">{akhirJml}</td>
                
                <td className="px-4 py-3"></td>
            </tr>
        );
    };

    return (
        <BukuLayout
            auth={auth}
            jenis_buku="buku-rekapitulasi-penduduk"
            judul="Buku Rekapitulasi Jumlah Penduduk"
            data={data}
            filters={{...filters, bulan, tahun}}
            tableHead={tableHead}
            renderRow={renderRow}
            hasStandardFilter={false}
            customFilter={customFilter}
        />
    );
}

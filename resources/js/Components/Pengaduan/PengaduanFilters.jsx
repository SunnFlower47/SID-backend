import React, { useState } from 'react';
import { Search, Filter, RefreshCw } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function PengaduanFilters({ filters = {} }) {
    const [showFilters, setShowFilters] = useState(
        filters.search || filters.status || filters.prioritas || filters.kategori ? true : false
    );
    const [local, setLocal] = useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
        prioritas: filters.prioritas ?? '',
        kategori: filters.kategori ?? '',
    });

    const handleApply = () => {
        const finalFilters = { ...local };
        Object.keys(finalFilters).forEach(k => {
            if (finalFilters[k] === '') {
                delete finalFilters[k];
            }
        });
        router.get(route('pengaduan.index'), finalFilters, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleReset = () => {
        setLocal({ search: '', status: '', prioritas: '', kategori: '' });
        router.get(route('pengaduan.index'));
    };

    return (
        <div className="mb-6 space-y-4">
            <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                <div className="flex items-center gap-2 sm:gap-4">
                    <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">Konfigurasi Data</h3>
                        <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter Pengaduan</p>
                    </div>
                </div>
                <button
                    onClick={() => setShowFilters(!showFilters)}
                    className={cn(
                        "flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95",
                        showFilters
                            ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                            : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                    )}
                >
                    <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                    {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                </button>
            </div>

            {showFilters && (
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 animate-in slide-in-from-top-2 duration-300">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <div className="lg:col-span-1">
                            <input type="text" value={local.search} placeholder="Cari judul atau pelapor..."
                                onChange={e => setLocal({ ...local, search: e.target.value })}
                                onKeyDown={e => e.key === 'Enter' && handleApply()}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                        <select value={local.status} onChange={e => setLocal({ ...local, status: e.target.value })} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="baru">Baru</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <select value={local.prioritas} onChange={e => setLocal({ ...local, prioritas: e.target.value })} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Prioritas</option>
                            <option value="rendah">Rendah</option>
                            <option value="sedang">Sedang</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="darurat">Darurat</option>
                        </select>
                        <select value={local.kategori} onChange={e => setLocal({ ...local, kategori: e.target.value })} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Kategori</option>
                            <option value="infrastruktur">Infrastruktur</option>
                            <option value="keamanan">Keamanan</option>
                            <option value="kebersihan">Kebersihan</option>
                            <option value="administrasi">Administrasi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    
                    <div className="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-3 sm:mt-4">
                        <button onClick={handleReset} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-gray-50 text-gray-500 text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-gray-100 hover:text-gray-700 transition-all border border-gray-200">
                            <RefreshCw className="w-3.5 h-3.5 inline-block mr-2" /> RESET FILTER
                        </button>
                        <button onClick={handleApply} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-green-600 text-white text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 active:scale-95">
                            <Filter className="w-3.5 h-3.5 inline-block mr-2" /> TERAPKAN FILTER
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

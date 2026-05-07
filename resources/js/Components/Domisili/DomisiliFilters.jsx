import React, { useState } from 'react';
import { Search, Filter, RefreshCw } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function DomisiliFilters({ filters = {}, rtList = [], rwList = [] }) {
    const [showFilters, setShowFilters] = useState(Object.values(filters).some(Boolean));
    const [filterData, setFilterData] = useState(filters);

    const updateFilter = (key, val) => {
        setFilterData(prev => ({ ...prev, [key]: val, page: 1 }));
    };

    const handleApply = () => {
        router.get(route('domisili.index'), filterData, { preserveState: true, replace: true });
    };

    const resetFilter = () => {
        setFilterData({});
        router.get(route('domisili.index'), {}, { preserveState: false });
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
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter Domisili</p>
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
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
                        <div className="lg:col-span-2 xl:col-span-1">
                            <input type="text" value={filterData.search || ''} placeholder="Cari nama / NIK / asal daerah..."
                                onChange={e => updateFilter('search', e.target.value)}
                                onKeyDown={e => e.key === 'Enter' && handleApply()}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                        <select value={filterData.status || ''} onChange={e => updateFilter('status', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="expired">Expired</option>
                            <option value="dicabut">Dicabut</option>
                        </select>
                        <select value={filterData.rw_id || ''} onChange={e => updateFilter('rw_id', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua RW</option>
                            {rwList?.map(rw => <option key={rw.id} value={rw.id}>RW {rw.kode}</option>)}
                        </select>
                        <select value={filterData.rt_id || ''} onChange={e => updateFilter('rt_id', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua RT</option>
                            {rtList?.map(rt => <option key={rt.id} value={rt.id}>RT {rt.kode}</option>)}
                        </select>
                        <select value={filterData.keperluan_domisili || ''} onChange={e => updateFilter('keperluan_domisili', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Keperluan</option>
                            <option value="kerja">Kerja</option>
                            <option value="sekolah">Sekolah</option>
                            <option value="ikut_keluarga">Ikut Keluarga</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    
                    <div className="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-3 sm:mt-4">
                        <button onClick={resetFilter} className="flex-1 sm:flex-none items-center justify-center gap-2 px-6 py-2 rounded-xl bg-gray-50 text-gray-500 text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-gray-100 hover:text-gray-700 transition-all border border-gray-200">
                            <RefreshCw className="w-3.5 h-3.5" /> RESET FILTER
                        </button>
                        <button onClick={handleApply} className="flex-1 sm:flex-none items-center justify-center gap-2 px-6 py-2 rounded-xl bg-green-600 text-white text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 active:scale-95">
                            <Filter className="w-3.5 h-3.5" /> TERAPKAN FILTER
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

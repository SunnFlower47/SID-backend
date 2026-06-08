import React, { useState } from 'react';
import { Search, Filter, RefreshCw } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function ResidentFilters({ filters, rtList, rwList, dusunList = [], submitRouteName = 'penduduk.index', submitRouteParams = {} }) {
    const [showFilters, setShowFilters] = useState(
        filters.search || filters.rt_id || filters.rw_id || filters.dusun_id || filters.jenis_kelamin || filters.filter_umur ? true : false
    );
    const [localFilters, setLocalFilters] = useState({
        search: filters.search || '',
        rt_id: filters.rt_id || filters.rt || '',
        rw_id: filters.rw_id || filters.rw || '',
        dusun_id: filters.dusun_id || filters.dusun || '',
        jenis_kelamin: filters.jenis_kelamin || '',
        filter_umur: filters.filter_umur || ''
    });

    const updateLocalFilter = (key, value) => {
        setLocalFilters(prev => ({ ...prev, [key]: value }));
    };

    const handleApply = () => {
        const finalFilters = { ...localFilters };
        Object.keys(finalFilters).forEach(k => {
            if (finalFilters[k] === '' || finalFilters[k] === null) {
                delete finalFilters[k];
            }
        });
        router.get(route(submitRouteName, submitRouteParams), finalFilters, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleReset = () => {
        setLocalFilters({ search: '', rt_id: '', rw_id: '', dusun_id: '', jenis_kelamin: '', filter_umur: '' });
        router.get(route(submitRouteName, submitRouteParams));
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
                        <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter Penduduk</p>
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
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
                        <div className="lg:col-span-2 xl:col-span-2">
                            <input type="text" value={localFilters.search} placeholder="Cari nama / NIK / No KK..."
                                onChange={e => updateLocalFilter('search', e.target.value)}
                                onKeyDown={e => e.key === 'Enter' && handleApply()}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                        <select value={localFilters.dusun_id} onChange={e => updateLocalFilter('dusun_id', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Wilayah</option>
                            {dusunList.map(dusun => <option key={dusun.id} value={dusun.id}>{dusun.nama}</option>)}
                        </select>
                        <select value={localFilters.rw_id} onChange={e => updateLocalFilter('rw_id', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua RW</option>
                            {rwList.map(rw => <option key={rw.id} value={rw.id}>RW {rw.kode}</option>)}
                        </select>
                        <select value={localFilters.rt_id} onChange={e => updateLocalFilter('rt_id', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua RT</option>
                            {rtList.map(rt => <option key={rt.id} value={rt.id}>RT {rt.kode}</option>)}
                        </select>
                        <select value={localFilters.jenis_kelamin} onChange={e => updateLocalFilter('jenis_kelamin', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                            <option value="">Semua Gender</option>
                            <option value="LAKI-LAKI">Laki-laki</option>
                            <option value="PEREMPUAN">Perempuan</option>
                        </select>
                        <select value={localFilters.filter_umur} onChange={e => updateLocalFilter('filter_umur', e.target.value)} className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer xl:col-start-6 xl:row-start-1">
                            <option value="">Semua Umur</option>
                            <optgroup label="Kelompok Usia">
                                <option value="bayi">Bayi (0-1 thn)</option>
                                <option value="balita">Balita (2-5 thn)</option>
                                <option value="anak">Anak (6-12 thn)</option>
                                <option value="remaja">Remaja (13-17 thn)</option>
                                <option value="dewasa_muda">Dewasa Muda (18-25 thn)</option>
                                <option value="dewasa">Dewasa (26-59 thn)</option>
                                <option value="lansia">Lansia (≥60 thn)</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div className="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-3 sm:mt-4">
                        <button onClick={handleReset} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-gray-50 text-gray-500 text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-gray-100 hover:text-gray-700 transition-all border border-gray-200">
                            <RefreshCw className="w-3.5 h-3.5" /> RESET FILTER
                        </button>
                        <button onClick={handleApply} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-green-600 text-white text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 active:scale-95">
                            <Filter className="w-3.5 h-3.5" /> TERAPKAN FILTER
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

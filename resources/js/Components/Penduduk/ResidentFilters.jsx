import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Search, Filter, RefreshCw } from 'lucide-react';

export default function ResidentFilters({ filters, rtList, rwList, dusunList = [] }) {
    // Local state for filters to avoid realtime requests
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
        
        // Remove empty filters
        Object.keys(finalFilters).forEach(k => {
            if (finalFilters[k] === '' || finalFilters[k] === null) {
                delete finalFilters[k];
            }
        });

        router.get(route('penduduk.index'), finalFilters, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleReset = () => {
        const emptyFilters = {
            search: '',
            rt_id: '',
            rw_id: '',
            dusun_id: '',
            jenis_kelamin: '',
            filter_umur: ''
        };
        setLocalFilters(emptyFilters);
        router.get(route('penduduk.index'));
    };

    return (
        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-7 mb-6">
            <div className="flex items-center justify-between gap-3 mb-6">
                <h3 className="text-sm sm:text-base font-black text-gray-950 flex items-center uppercase italic tracking-tighter">
                    <Filter className="text-green-500 mr-2 sm:mr-3 w-5 h-5 sm:w-6 sm:h-6" />
                    Konfigurasi Pencarian & Filter
                </h3>
                <button 
                    onClick={handleReset}
                    className="text-[10px] sm:text-xs font-black text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors flex items-center gap-1.5"
                >
                    <RefreshCw className="w-3 h-3" />
                    Reset Filter
                </button>
            </div>

            <div className="space-y-6">
                {/* Search Input */}
                <div>
                    <label className="block text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                        Kata Kunci Pencarian
                    </label>
                    <div className="relative">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Search className="text-gray-400 w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <input 
                            type="text" 
                            value={localFilters.search}
                            onChange={(e) => updateLocalFilter('search', e.target.value)}
                            onKeyPress={(e) => e.key === 'Enter' && handleApply()}
                            placeholder="Cari NIK, nama lengkap, atau No KK..."
                            className="w-full pl-12 pr-4 py-3 sm:py-3.5 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 text-sm sm:text-base bg-gray-50/50 font-bold placeholder:font-medium transition-all"
                        />
                    </div>
                </div>

                {/* Filter Options */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    {/* Dusun Filter */}
                    <div>
                        <label className="block text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Wilayah Dusun</label>
                        <select 
                            value={localFilters.dusun_id} 
                            onChange={(e) => updateLocalFilter('dusun_id', e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 text-sm font-bold bg-white appearance-none cursor-pointer"
                        >
                            <option value="">Semua Wilayah</option>
                            {dusunList.map(dusun => (
                                <option key={dusun.id} value={dusun.id}>{dusun.nama}</option>
                            ))}
                        </select>
                    </div>

                    {/* RW Filter */}
                    <div>
                        <label className="block text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">RW</label>
                        <select 
                            value={localFilters.rw_id} 
                            onChange={(e) => updateLocalFilter('rw_id', e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 text-sm font-bold bg-white appearance-none cursor-pointer"
                        >
                            <option value="">Semua RW</option>
                            {rwList.map(rw => (
                                <option key={rw.id} value={rw.id}>RW {rw.kode}</option>
                            ))}
                        </select>
                    </div>

                    {/* RT Filter */}
                    <div>
                        <label className="block text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">RT</label>
                        <select 
                            value={localFilters.rt_id} 
                            onChange={(e) => updateLocalFilter('rt_id', e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-sm font-bold bg-white appearance-none cursor-pointer"
                        >
                            <option value="">Semua RT</option>
                            {rtList.map(rt => (
                                <option key={rt.id} value={rt.id}>RT {rt.kode}</option>
                            ))}
                        </select>
                    </div>

                    {/* Jenis Kelamin Filter */}
                    <div>
                        <label className="block text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Jenis Kelamin</label>
                        <select 
                            value={localFilters.jenis_kelamin} 
                            onChange={(e) => updateLocalFilter('jenis_kelamin', e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-pink-500/10 focus:border-pink-500 text-sm font-bold bg-white appearance-none cursor-pointer"
                        >
                            <option value="">Semua Gender</option>
                            <option value="LAKI-LAKI">Laki-laki</option>
                            <option value="PEREMPUAN">Perempuan</option>
                        </select>
                    </div>

                    {/* Filter Umur */}
                    <div>
                        <label className="block text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kategori Umur</label>
                        <select 
                            value={localFilters.filter_umur} 
                            onChange={(e) => updateLocalFilter('filter_umur', e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 text-sm font-bold bg-white appearance-none cursor-pointer"
                        >
                            <option value="">Semua Umur</option>
                            <optgroup label="Kelompok Usia">
                                <option value="bayi">Bayi (0-1 tahun)</option>
                                <option value="balita">Balita (2-5 tahun)</option>
                                <option value="anak">Anak (6-12 tahun)</option>
                                <option value="remaja">Remaja (13-17 tahun)</option>
                                <option value="dewasa_muda">Dewasa Muda (18-25 tahun)</option>
                                <option value="dewasa">Dewasa (26-59 tahun)</option>
                                <option value="lansia">Lansia (≥60 tahun)</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                {/* Apply Button */}
                <div className="flex justify-center sm:justify-end pt-4 border-t border-gray-50 mt-4">
                    <button 
                        onClick={handleApply}
                        className="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-all text-xs font-bold uppercase tracking-widest shadow-md shadow-green-200 active:scale-95 group"
                    >
                        <Filter className="mr-2 w-3.5 h-3.5 sm:w-4 h-4 group-hover:rotate-12 transition-transform" />
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    );
}

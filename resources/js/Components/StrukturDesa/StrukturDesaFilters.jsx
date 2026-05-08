import React, { useState, useEffect } from 'react';
import { Search, Filter, RefreshCw, ChevronDown } from 'lucide-react';
import { router } from '@inertiajs/react';

export default function StrukturDesaFilters({ filters = {}, kategoriOptions = [] }) {
    const [local, setLocal] = useState({
        search: filters.search ?? '',
        kategori: filters.kategori ?? '',
    });

    useEffect(() => {
        const timer = setTimeout(() => {
            const currentSearch = filters.search || '';
            const currentKategori = filters.kategori || '';

            if (local.search !== currentSearch || local.kategori !== currentKategori) {
                router.get(
                    route('struktur-desa.index'),
                    local,
                    { preserveState: true, preserveScroll: true, replace: true }
                );
            }
        }, 500);

        return () => clearTimeout(timer);
    }, [local.search, local.kategori, filters.search, filters.kategori]);

    const handleReset = () => {
        setLocal({ search: '', kategori: '' });
        router.get(route('struktur-desa.index'));
    };

    const isFiltered = local.search !== '' || local.kategori !== '';

    return (
        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-4 mb-4 shadow-black/5 text-left">
            <div className="flex flex-col lg:flex-row gap-3">
                {/* Search */}
                <div className="flex-1">
                    <div className="relative group">
                        <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors group-focus-within:text-green-500">
                            <Search className="w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                        </div>
                        <input
                            type="text"
                            value={local.search}
                            onChange={(e) => setLocal({ ...local, search: e.target.value })}
                            className="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 rounded-xl sm:rounded-2xl text-[10px] sm:text-[11px] font-black transition-all placeholder:font-bold placeholder:text-gray-400 uppercase tracking-widest"
                            placeholder="CARI NAMA ATAU JABATAN..."
                        />
                    </div>
                </div>

                {/* Filter Kategori */}
                <div className="flex-1 lg:max-w-xs">
                    <div className="relative group">
                        <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors group-focus-within:text-green-500">
                            <Filter className="w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                        </div>
                        <select
                            value={local.kategori}
                            onChange={(e) => setLocal({ ...local, kategori: e.target.value })}
                            className="w-full pl-10 pr-10 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 rounded-xl sm:rounded-2xl text-[10px] sm:text-[11px] font-black uppercase tracking-widest transition-all appearance-none text-gray-700 cursor-pointer"
                        >
                            <option value="">SEMUA JABATAN</option>
                            {kategoriOptions.map((opt) => (
                                <option key={opt.value} value={opt.value} className="font-bold">
                                    {opt.label.toUpperCase()}
                                </option>
                            ))}
                        </select>
                        <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none transition-colors group-focus-within:text-green-500">
                            <ChevronDown className="w-3.5 h-3.5 text-gray-400 group-focus-within:text-green-500" />
                        </div>
                    </div>
                </div>

                {/* Reset Filters */}
                {isFiltered && (
                    <button
                        onClick={handleReset}
                        className="px-5 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-xl sm:rounded-2xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 active:scale-95 shadow-sm shadow-red-100"
                    >
                        <RefreshCw className="w-3 h-3" />
                        RESET
                    </button>
                )}
            </div>
        </div>
    );
}

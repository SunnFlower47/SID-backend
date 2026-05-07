import React, { useState } from 'react';
import { Search, Filter } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function MutasiFilters({ filters = {} }) {
    const [tempSearch, setTempSearch] = useState(filters.search || '');
    const [jenisFilter, setJenisFilter] = useState(filters.jenis_mutasi || 'all');
    const [showFilters, setShowFilters] = useState(filters.search || filters.jenis_mutasi ? true : false);

    const handleSearch = () => {
        router.get(
            route('mutasi.data.index'), 
            { search: tempSearch, jenis_mutasi: jenisFilter === 'all' ? '' : jenisFilter }, 
            { preserveState: true, replace: true }
        );
    };

    const handleFilterChange = (val) => {
        setJenisFilter(val);
    };

    return (
        <div className="mb-6 space-y-4">
            {/* Filter Toggle Section */}
            <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                <div className="flex items-center gap-2 sm:gap-4">
                    <div className="w-8 h-8 sm:w-10 sm:h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter">Konfigurasi Data</h3>
                        <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pencarian & Filter Mutasi</p>
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

            {/* Filters Content */}
            {showFilters && (
                <div className="bg-white rounded-2xl border border-gray-100 shadow-xl p-4 sm:p-5 animate-in slide-in-from-top-4 duration-500">
                    <div className="flex flex-col md:flex-row gap-6 items-end justify-between">
                        <div className="w-full md:flex-1 space-y-4">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cari Mutasi</label>
                                <div className="relative">
                                    <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input
                                        type="text"
                                        placeholder="Nama Warga, NIK, atau Alasan Mutasi..."
                                        className="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none transition-all focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500"
                                        value={tempSearch}
                                        onChange={(e) => setTempSearch(e.target.value)}
                                        onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="w-full md:w-64 space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Mutasi</label>
                            <select
                                className="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:bg-white focus:border-blue-500 appearance-none"
                                value={jenisFilter}
                                onChange={(e) => handleFilterChange(e.target.value)}
                            >
                                <option value="all">SEMUA JENIS</option>
                                <option value="kelahiran">KELAHIRAN</option>
                                <option value="kematian">KEMATIAN</option>
                                <option value="pindah_masuk">PINDAH MASUK</option>
                                <option value="pindah_keluar">PINDAH KELUAR</option>
                                <option value="pindah_rt_rw">PINDAH RT/RW</option>
                                <option value="pisah_kk">PISAH KK</option>
                            </select>
                        </div>

                        <button
                            onClick={handleSearch}
                            className="w-full md:w-auto px-10 py-2.5 bg-blue-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all active:scale-95"
                        >
                            Terapkan
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

import React, { useState } from 'react';
import { Search, Filter, RefreshCw } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function TestimoniFilters({ filters = {} }) {
    const [showFilters, setShowFilters] = useState(Object.values(filters).some(Boolean));
    const [filterData, setFilterData] = useState(filters);

    const updateFilter = (key, val) => {
        setFilterData(prev => ({ ...prev, [key]: val, page: 1 }));
    };

    const handleApply = () => {
        router.get(route('testimoni.index'), filterData, { preserveState: true, replace: true });
    };

    const resetFilter = () => {
        setFilterData({});
        router.get(route('testimoni.index'), {}, { preserveState: false });
    };

    return (
        <div className="mb-6 space-y-4">
            <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                <div className="flex items-center gap-2 sm:gap-4 text-left">
                    <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1">Konfigurasi Data</h3>
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pencarian & Filter Testimoni</p>
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
                    {showFilters ? 'TUTUP FILTER' : 'BUKA FILTER'}
                </button>
            </div>

            {showFilters && (
                <div className="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm animate-in slide-in-from-top-4 duration-300">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Cari Kata Kunci</label>
                            <input
                                type="text"
                                value={filterData.search || ''}
                                onChange={e => updateFilter('search', e.target.value)}
                                placeholder="Nama atau isi testimoni..."
                                className="w-full px-5 py-3 bg-gray-50 border-none focus:ring-2 focus:ring-green-500 rounded-xl text-sm font-bold text-gray-700 shadow-inner"
                            />
                        </div>
                        <div>
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Filter Status</label>
                            <select
                                value={filterData.status || ''}
                                onChange={e => updateFilter('status', e.target.value)}
                                className="w-full px-5 py-3 bg-gray-50 border-none focus:ring-2 focus:ring-green-500 rounded-xl text-sm font-bold text-gray-700 shadow-inner appearance-none"
                            >
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Rating Bintang</label>
                            <select
                                value={filterData.rating || ''}
                                onChange={e => updateFilter('rating', e.target.value)}
                                className="w-full px-5 py-3 bg-gray-50 border-none focus:ring-2 focus:ring-green-500 rounded-xl text-sm font-bold text-gray-700 shadow-inner appearance-none"
                            >
                                <option value="">Semua Rating</option>
                                {[5, 4, 3, 2, 1].map(r => (
                                    <option key={r} value={r}>{r} Bintang</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-50">
                        <button
                            onClick={resetFilter}
                            className="flex items-center px-6 py-3 rounded-xl text-xs font-black text-gray-400 hover:text-gray-600 transition-all uppercase tracking-widest"
                        >
                            <RefreshCw className="w-4 h-4 mr-2" />
                            RESET
                        </button>
                        <button
                            onClick={handleApply}
                            className="flex items-center px-10 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black shadow-lg shadow-green-100 transition-all active:scale-95 uppercase tracking-widest"
                        >
                            TERAPKAN FILTER
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

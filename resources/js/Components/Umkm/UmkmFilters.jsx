import React, { useState } from 'react';
import { Search, Filter, RefreshCw, Star } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function UmkmFilters({ filters = {}, jenisOptions = [] }) {
    const [showFilters, setShowFilters] = useState(
        filters.search || filters.status || filters.jenis_usaha || filters.is_unggulan ? true : false
    );
    
    const [local, setLocal] = useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
        jenis_usaha: filters.jenis_usaha ?? '',
        is_unggulan: filters.is_unggulan ?? '',
    });

    const updateLocal = (key, value) => {
        setLocal(prev => ({ ...prev, [key]: value }));
    };

    const handleApply = () => {
        router.get(route('umkm.index'), local, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleReset = () => {
        setLocal({ search: '', status: '', jenis_usaha: '', is_unggulan: '' });
        router.get(route('umkm.index'));
    };

    return (
        <div className="mb-6 space-y-4 text-left">
            <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all text-left">
                <div className="flex items-center gap-2 sm:gap-4 text-left">
                    <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center text-left">
                        <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                    </div>
                    <div className="text-left">
                        <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">Konfigurasi Data</h3>
                        <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter UMKM Desa</p>
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
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 animate-in slide-in-from-top-2 duration-300 text-left">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-left">
                        <div className="lg:col-span-1 text-left">
                            <input 
                                type="text" 
                                value={local.search} 
                                placeholder="Cari usaha atau pemilik..."
                                onChange={e => updateLocal('search', e.target.value)}
                                onKeyDown={e => e.key === 'Enter' && handleApply()}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500 text-left" 
                            />
                        </div>

                        <div className="text-left">
                            <select 
                                value={local.jenis_usaha} 
                                onChange={e => updateLocal('jenis_usaha', e.target.value)} 
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer text-left"
                            >
                                <option value="">Semua Jenis Usaha</option>
                                {jenisOptions.map(opt => <option key={opt.value} value={opt.value}>{opt.label}</option>)}
                            </select>
                        </div>

                        <div className="text-left">
                            <select 
                                value={local.status} 
                                onChange={e => updateLocal('status', e.target.value)} 
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer text-left"
                            >
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="tutup">Tutup</option>
                                <option value="pindah">Pindah</option>
                            </select>
                        </div>

                        <div className="text-left">
                            <select 
                                value={local.is_unggulan} 
                                onChange={e => updateLocal('is_unggulan', e.target.value)} 
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer text-left"
                            >
                                <option value="">Semua UMKM</option>
                                <option value="true">UMKM Unggulan</option>
                                <option value="false">Bukan Unggulan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div className="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-3 sm:mt-4 text-left">
                        <button onClick={handleReset} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-gray-50 text-gray-500 text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-gray-100 hover:text-gray-700 transition-all border border-gray-200 text-left">
                            <RefreshCw className="w-3.5 h-3.5" /> RESET FILTER
                        </button>
                        <button onClick={handleApply} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-green-600 text-white text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 active:scale-95 text-left">
                            <Filter className="w-3.5 h-3.5" /> TERAPKAN FILTER
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

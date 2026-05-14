import React, { useState } from 'react';
import { Search, Filter, RefreshCw } from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';

const SUMBER_DANA_OPTIONS = [
    { value: 'dana_desa_ad', label: 'Dana Desa - Alokasi Dasar (AD)' },
    { value: 'dana_desa_af', label: 'Dana Desa - Alokasi Formula (AF)' },
    { value: 'dana_desa_ak', label: 'Dana Desa - Alokasi Kinerja (AK)' },
    { value: 'dau',          label: 'Dana Alokasi Umum (DAU)' },
    { value: 'dak',          label: 'Dana Alokasi Khusus (DAK)' },
    { value: 'dbh',          label: 'Dana Bagi Hasil (DBH)' },
    { value: 'did',          label: 'Dana Insentif Daerah (DID)' },
    { value: 'pad',          label: 'Pendapatan Asli Desa (PAD)' },
];

export default function KeuanganFilters({ filters = {}, tahunList = [], routeName = 'transparansi-desa.apbdes', showSumberDana = true, showJenis = true, showStatus = false }) {
    const [showFilters, setShowFilters] = useState(
        !!(filters.search || filters.jenis || filters.sumber_dana || filters.status)
    );

    const [local, setLocal] = useState({
        tahun:       filters.tahun       ?? new Date().getFullYear(),
        search:      filters.search      ?? '',
        jenis:       filters.jenis       ?? '',
        sumber_dana: filters.sumber_dana ?? '',
        status:      filters.status      ?? '',
    });

    const update = (key, value) => setLocal(prev => ({ ...prev, [key]: value }));

    const handleApply = () => {
        router.get(route(routeName), local, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const handleReset = () => {
        const reset = { tahun: local.tahun, search: '', jenis: '', sumber_dana: '', status: '' };
        setLocal(reset);
        router.get(route(routeName), reset, { replace: true });
    };

    return (
        <div className="mb-6 space-y-4">
            {/* Header Row */}
            <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm">
                <div className="flex items-center gap-2 sm:gap-4">
                    <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1">Konfigurasi Keuangan</h3>
                        <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pencarian & Filter Data APBDes</p>
                    </div>
                </div>
                <button
                    onClick={() => setShowFilters(!showFilters)}
                    className={cn(
                        'flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95',
                        showFilters
                            ? 'bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20'
                            : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'
                    )}
                >
                    <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                    {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                </button>
            </div>

            {showFilters && (
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 animate-in slide-in-from-top-2 duration-300">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        {/* Search */}
                        <div className="lg:col-span-1">
                            <input
                                type="text"
                                value={local.search}
                                placeholder="Cari kode / nama rekening..."
                                onChange={e => update('search', e.target.value)}
                                onKeyDown={e => e.key === 'Enter' && handleApply()}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            />
                        </div>

                        {/* Tahun */}
                        <select
                            value={local.tahun}
                            onChange={e => update('tahun', e.target.value)}
                            className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                        >
                            {(tahunList.length ? tahunList : [new Date().getFullYear()]).map(t => (
                                <option key={t} value={t}>{t}</option>
                            ))}
                        </select>

                        {/* Jenis */}
                        {showJenis && (
                            <select
                                value={local.jenis}
                                onChange={e => update('jenis', e.target.value)}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                            >
                                <option value="">Semua Jenis</option>
                                <option value="pendapatan">Pendapatan</option>
                                <option value="belanja">Belanja</option>
                                <option value="pembiayaan">Pembiayaan</option>
                            </select>
                        )}

                        {/* Sumber Dana */}
                        {showSumberDana && (
                            <select
                                value={local.sumber_dana}
                                onChange={e => update('sumber_dana', e.target.value)}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                            >
                                <option value="">Semua Sumber Dana</option>
                                {SUMBER_DANA_OPTIONS.map(opt => (
                                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                                ))}
                            </select>
                        )}

                        {/* Status (for proyek) */}
                        {showStatus && (
                            <select
                                value={local.status}
                                onChange={e => update('status', e.target.value)}
                                className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                            >
                                <option value="">Semua Status</option>
                                <option value="perencanaan">Perencanaan</option>
                                <option value="pelaksanaan">Pelaksanaan</option>
                                <option value="selesai">Selesai</option>
                                <option value="tertunda">Tertunda</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        )}
                    </div>

                    <div className="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-3 sm:mt-4">
                        <button onClick={handleReset} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-gray-50 text-gray-500 text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-gray-100 border border-gray-200 transition-all">
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

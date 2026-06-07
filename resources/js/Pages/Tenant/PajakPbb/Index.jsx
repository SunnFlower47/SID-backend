import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { Landmark, Search, Eye, RefreshCw, Wallet, TrendingUp, Filter, Trash2 } from 'lucide-react';
import { PageHeader, TableCard, EmptyState, Badge, StatCard } from '@/Components/Shared';
import { useSwalDelete } from '@/lib/useSwalDelete';

export default function Index({ auth, objeks, stats, filters }) {
    const [showFilters, setShowFilters] = useState(
        filters.search || filters.status_sync ? true : false
    );
    const [localFilters, setLocalFilters] = useState({
        search: filters.search || '',
        status_sync: filters.status_sync || ''
    });

    const { post, processing } = useForm();
    const confirmDelete = useSwalDelete();

    const handleDelete = (id, nop) => {
        confirmDelete(`NOP: ${nop}`, () => {
            router.delete(route('pajak-pbb.destroy', id), {
                preserveScroll: true,
            });
        });
    };

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
        router.get(route('pajak-pbb.index'), finalFilters, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleReset = () => {
        setLocalFilters({ search: '', status_sync: '' });
        router.get(route('pajak-pbb.index'));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        handleApply();
    };

    const handleSync = (id) => {
        post(route('pajak-pbb.sync', id), {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Data Pajak PBB"
        >
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader 
                    title="Pajak Bumi dan Bangunan" 
                    subtitle="Kelola sinkronisasi data tagihan PBB masyarakat dari sistem pusat."
                    icon={Landmark}
                />

                {/* Statistics Cards */}
                {stats && stats.length > 0 && (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {stats.map((stat, index) => {
                            const percentage = stat.total_potensi > 0 
                                ? Math.round((stat.total_realisasi / stat.total_potensi) * 100) 
                                : 0;
                            
                            // Alternate colors for variety
                            const colors = ['green', 'blue', 'purple', 'emerald'];
                            const color = colors[index % colors.length];

                            return (
                                <StatCard
                                    key={stat.tahun}
                                    icon={Wallet}
                                    label={`PBB Tahun ${stat.tahun}`}
                                    value={`Rp ${Number(stat.total_realisasi).toLocaleString('id-ID')}`}
                                    sub={`Potensi: Rp ${Number(stat.total_potensi).toLocaleString('id-ID')}`}
                                    badge={`${percentage}% Lunas`}
                                    color={color}
                                />
                            );
                        })}
                    </div>
                )}

                {/* Filter / Search Bar (Advanced Filter Panel) */}
                <div className="mb-6 space-y-4">
                    <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                        <div className="flex items-center gap-2 sm:gap-4">
                            <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                                <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                            </div>
                            <div>
                                <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">Konfigurasi Data</h3>
                                <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter PBB</p>
                            </div>
                        </div>
                        <button
                            onClick={() => setShowFilters(!showFilters)}
                            className={`flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95 ${
                                showFilters
                                    ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                                    : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                            }`}
                        >
                            <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                            {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                        </button>
                    </div>

                    {showFilters && (
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 animate-in slide-in-from-top-2 duration-300">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <div>
                                    <input 
                                        type="text" 
                                        value={localFilters.search} 
                                        placeholder="Cari NOP / Nama Wajib Pajak..."
                                        onChange={e => updateLocalFilter('search', e.target.value)}
                                        onKeyDown={e => e.key === 'Enter' && handleApply()}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    />
                                </div>
                                <div>
                                    <select 
                                        value={localFilters.status_sync} 
                                        onChange={e => updateLocalFilter('status_sync', e.target.value)} 
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                    >
                                        <option value="">Semua Status Sinkronisasi</option>
                                        <option value="sudah">Sudah Disinkronisasi</option>
                                        <option value="belum">Belum Disinkronisasi</option>
                                    </select>
                                </div>
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

                {/* Table Data */}
                <TableCard
                    title="Daftar Wajib Pajak"
                    icon={Landmark}
                    total={objeks?.total || 0}
                    pagination={objeks}
                    noPadding={true}
                >
                    {objeks?.data?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full whitespace-nowrap text-left text-sm text-gray-600">
                                <thead className="bg-gray-50 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200">
                                    <tr>
                                        <th className="px-4 py-3 text-center border-r border-gray-200">NO</th>
                                        <th className="px-4 py-3 border-r border-gray-200 text-center">AKSI</th>
                                        <th className="px-4 py-3 border-r border-gray-200">NOP</th>
                                        <th className="px-4 py-3 border-r border-gray-200">NAMA WAJIB PAJAK</th>
                                        <th className="px-4 py-3 border-r border-gray-200 text-center">LUAS (BUMI/BGN)</th>
                                        <th className="px-4 py-3 border-r border-gray-200 text-center">STATUS SINKRONISASI</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {objeks.data.map((objek, index) => {
                                        const nomorUrut = objeks.from ? objeks.from + index : index + 1;
                                        return (
                                            <tr key={objek.id} className="hover:bg-green-50/30 transition-colors">
                                                <td className="px-4 py-3 text-center font-mono text-xs">{nomorUrut}</td>
                                                <td className="px-4 py-3 text-center border-r border-gray-50 flex justify-center gap-2">
                                                    <Link 
                                                        href={route('pajak-pbb.show', objek.id)}
                                                        className="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors"
                                                        title="Detail"
                                                    >
                                                        <Eye size={16} />
                                                    </Link>
                                                    <button 
                                                        onClick={() => handleSync(objek.id)} 
                                                        disabled={processing}
                                                        className="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-100 transition-colors disabled:opacity-50"
                                                        title="Sinkronisasi Manual"
                                                    >
                                                        <RefreshCw size={16} className={processing ? "animate-spin" : ""} />
                                                    </button>
                                                    <button 
                                                        onClick={() => handleDelete(objek.id, objek.nop)} 
                                                        disabled={processing}
                                                        className="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors disabled:opacity-50"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="font-mono text-xs font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded inline-block border border-gray-200">{objek.nop}</div>
                                                </td>
                                                <td className="px-4 py-3 font-bold text-gray-900">
                                                    {objek.nama_wp || <span className="italic text-gray-400 font-normal">Belum disinkronisasi</span>}
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    {objek.luas_bumi ? (
                                                        <div className="flex gap-2 justify-center items-center text-xs">
                                                            <span className="bg-green-50 text-green-700 px-2 py-0.5 rounded border border-green-100">{objek.luas_bumi} m²</span>
                                                            <span className="text-gray-300">/</span>
                                                            <span className="bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100">{objek.luas_bangunan} m²</span>
                                                        </div>
                                                    ) : '-'}
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    {objek.last_synced_at ? (
                                                        <Badge color="green" size="sm">
                                                            {new Date(objek.last_synced_at).toLocaleString('id-ID')}
                                                        </Badge>
                                                    ) : (
                                                        <Badge color="yellow" size="sm">Belum Sync</Badge>
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <EmptyState 
                            title="Belum Ada Data PBB"
                            message="Daftar objek PBB masih kosong atau tidak ditemukan dengan kata kunci pencarian Anda."
                        />
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}

import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, Search, Filter, ChevronLeft, ChevronRight as ChevronRightIcon, X, MapPin, Users, Hash } from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { PageHeader, FilterContainer } from '@/Components/Shared';

export default function KKIndex({ auth, kks, totalKK, filters, dusunOptions, rtOptions }) {

    const [localFilters, setLocalFilters] = React.useState(filters ?? {});

    const applyFilters = () => {
        router.get(route('laporan.kk'), localFilters, { preserveState: true, replace: true });
    };

    const resetFilters = () => {
        setLocalFilters({});
        router.get(route('laporan.kk'), {});
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Laporan KK">
            <Head title="Laporan Kartu Keluarga - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader 
                    icon={Home}
                    title="Laporan KK"
                    subtitle={`Total ${totalKK} Kartu Keluarga terdata`}
                    backHref={route('laporan.index')}
                />

                {/* ── Filter Panel ── */}
                <FilterContainer 
                    title="Filter Data KK" 
                    subtitle="Cari dan saring data laporan" 
                    hasActiveFilters={Object.keys(localFilters ?? {}).length > 0}
                >
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Filter Laporan</h3>
                            <button onClick={resetFilters} className="text-[9px] font-black text-red-400 uppercase tracking-widest flex items-center gap-1 hover:text-red-600 transition-all">
                                <X className="w-3 h-3" /> Reset
                            </button>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari NKK/Nama Kepala</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300" />
                                    <input value={localFilters.search ?? ''} onChange={e => setLocalFilters(p => ({...p, search: e.target.value}))} className="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" placeholder="Ketik nkk/nama..." />
                                </div>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Dusun</label>
                                <select value={localFilters.dusun_id ?? ''} onChange={e => setLocalFilters(p => ({...p, dusun_id: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 appearance-none">
                                    <option value="">Semua Dusun</option>
                                    {(dusunOptions ?? []).map(d => <option key={d.id} value={d.id}>{d.nama}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">RT</label>
                                <select value={localFilters.rt_id ?? ''} onChange={e => setLocalFilters(p => ({...p, rt_id: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 appearance-none">
                                    <option value="">Semua RT</option>
                                    {(rtOptions ?? []).map(r => <option key={r.id} value={r.id}>{r.kode}</option>)}
                                </select>
                            </div>
                        </div>
                        <button onClick={applyFilters} className="mt-4 px-6 py-2.5 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-md">Terapkan Filter</button>
                    </div>
                </FilterContainer>

                {/* ── Table ── */}
                <Deferred data="kks" fallback={<SkeletonTable columns={4} rows={10} />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['Nomor KK', 'Kepala Keluarga', 'Wilayah', 'Jumlah Anggota'].map(h => (
                                            <th key={h} className="px-6 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {(kks?.data ?? []).length > 0 ? (kks?.data ?? []).map((kk) => (
                                        <tr key={kk.id} className="hover:bg-gray-50/50 transition-all group">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2">
                                                    <Hash className="w-3.5 h-3.5 text-gray-300" />
                                                    <span className="text-xs font-black text-gray-900 font-mono tracking-tighter">{kk.nkk}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <p className="text-xs font-black text-gray-900 truncate">{kk.nama_kepala_keluarga ?? '—'}</p>
                                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">{kk.alamat || 'Alamat belum diset'}</p>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2 text-gray-600">
                                                    <MapPin className="w-3 h-3 text-blue-400" />
                                                    <span className="text-[10px] font-bold">
                                                        {kk.dusun_master?.nama ?? '—'} (RT {kk.rt_master?.kode ?? '—'} / RW {kk.rw_master?.kode ?? '—'})
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-left">
                                                <div className="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                                                    <Users className="w-3 h-3" />
                                                    <span className="text-[10px] font-black tracking-tighter">{kk.penduduks_count ?? '0'} Jiwa</span>
                                                </div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr><td colSpan={4} className="px-6 py-12 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest">Tidak ada data Kartu Keluarga ditemukan</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {kks?.last_page > 1 && (
                            <div className="p-5 border-t border-gray-50 flex items-center justify-between bg-gray-50/30">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Halaman {kks.current_page} dari {kks.last_page}</p>
                                <div className="flex gap-1">
                                    {kks.links.map((link, i) => (
                                        link.url ? (
                                            <Link 
                                                key={i} 
                                                href={link.url} 
                                                className={cn(
                                                    "w-8 h-8 flex items-center justify-center rounded-lg text-[10px] font-black transition-all",
                                                    link.active ? "bg-blue-600 text-white shadow-lg shadow-blue-200" : "bg-white text-gray-400 hover:bg-blue-50 hover:text-blue-600 border border-gray-100"
                                                )}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        ) : (
                                            <span key={i} className="w-8 h-8 flex items-center justify-center rounded-lg text-[10px] font-black text-gray-200 border border-gray-50 bg-white" dangerouslySetInnerHTML={{ __html: link.label }} />
                                        )
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

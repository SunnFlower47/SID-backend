    import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Newspaper, Search, Filter, ChevronLeft, ChevronRight as ChevronRightIcon, X, Calendar, User, Tag } from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { PageHeader, FilterContainer } from '@/Components/Shared';

export default function BeritaIndex({ auth, beritas, totalBerita, filters, kategoriOptions }) {

    const [localFilters, setLocalFilters] = React.useState(filters ?? {});

    const applyFilters = () => {
        router.get(route('laporan.berita'), localFilters, { preserveState: true, replace: true });
    };

    const resetFilters = () => {
        setLocalFilters({});
        router.get(route('laporan.berita'), {});
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Laporan Berita">
            <Head title="Laporan Berita - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader 
                    icon={Newspaper}
                    title="Laporan Berita"
                    subtitle={`Total ${totalBerita} konten dipublikasikan`}
                    backHref={route('laporan.index')}
                />

                {/* ── Filter Panel ── */}
                <FilterContainer 
                    title="Filter Data Laporan" 
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
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari Judul</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300" />
                                    <input value={localFilters.search ?? ''} onChange={e => setLocalFilters(p => ({...p, search: e.target.value}))} className="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" placeholder="Ketik judul..." />
                                </div>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Kategori</label>
                                <select value={localFilters.kategori ?? ''} onChange={e => setLocalFilters(p => ({...p, kategori: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 appearance-none">
                                    <option value="">Semua Kategori</option>
                                    {(kategoriOptions ?? []).map(k => <option key={k} value={k}>{k}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Dari Tanggal</label>
                                <input type="date" value={localFilters.start_date ?? ''} onChange={e => setLocalFilters(p => ({...p, start_date: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" />
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Sampai Tanggal</label>
                                <input type="date" value={localFilters.end_date ?? ''} onChange={e => setLocalFilters(p => ({...p, end_date: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" />
                            </div>
                        </div>
                        <button onClick={applyFilters} className="mt-4 px-6 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 transition-all shadow-md">Terapkan Filter</button>
                    </div>
                </FilterContainer>

                {/* ── Table ── */}
                <Deferred data="beritas" fallback={<SkeletonTable columns={4} rows={10} />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['Judul Berita', 'Kategori', 'Penulis', 'Tanggal Publish'].map(h => (
                                            <th key={h} className="px-6 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {(beritas?.data ?? []).length > 0 ? (beritas?.data ?? []).map((b) => (
                                        <tr key={b.id} className="hover:bg-gray-50/50 transition-all group">
                                            <td className="px-6 py-4">
                                                <p className="text-xs font-black text-gray-900 group-hover:text-orange-600 transition-colors line-clamp-1">{b.judul}</p>
                                                <div className="flex items-center gap-2 mt-1">
                                                    <Tag className="w-3 h-3 text-gray-300" />
                                                    <span className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{b.slug}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="px-2 py-1 bg-orange-50 text-orange-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-orange-100">
                                                    {b.kategori}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2">
                                                    <div className="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <User className="w-3 h-3 text-gray-400" />
                                                    </div>
                                                    <span className="text-[10px] font-bold text-gray-600">{b.author?.name ?? 'Admin'}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2">
                                                    <Calendar className="w-3 h-3 text-gray-300" />
                                                    <span className="text-[10px] font-bold text-gray-500">{new Date(b.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr><td colSpan={4} className="px-6 py-12 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest">Tidak ada berita yang ditemukan</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {beritas?.last_page > 1 && (
                            <div className="p-5 border-t border-gray-50 flex items-center justify-between bg-gray-50/30">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Halaman {beritas.current_page} dari {beritas.last_page}</p>
                                <div className="flex gap-1">
                                    {beritas.links.map((link, i) => (
                                        link.url ? (
                                            <Link 
                                                key={i} 
                                                href={link.url} 
                                                className={cn(
                                                    "w-8 h-8 flex items-center justify-center rounded-lg text-[10px] font-black transition-all",
                                                    link.active ? "bg-orange-600 text-white shadow-lg shadow-orange-200" : "bg-white text-gray-400 hover:bg-orange-50 hover:text-orange-600 border border-gray-100"
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

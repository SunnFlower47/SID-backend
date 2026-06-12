import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { FileText, Search, Filter, ChevronLeft, ChevronRight as ChevronRightIcon, X, Calendar, User, ShieldCheck, Clock, CheckCircle2, AlertCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { PageHeader, FilterContainer } from '@/Components/Shared';

const STATUS_CONFIG = {
    pending:   { color: 'text-orange-600', bg: 'bg-orange-50', border: 'border-orange-100', icon: Clock },
    proses:    { color: 'text-blue-600',   bg: 'bg-blue-50',   border: 'border-blue-100',   icon: AlertCircle },
    selesai:   { color: 'text-green-600',  bg: 'bg-green-50',  border: 'border-green-100',  icon: CheckCircle2 },
    ditolak:   { color: 'text-red-600',    bg: 'bg-red-50',    border: 'border-red-100',    icon: X },
};

export default function SuratIndex({ auth, surats, totalSurat, filters, jenisSuratOptions, statusOptions }) {

    const [localFilters, setLocalFilters] = React.useState(filters ?? {});

    const applyFilters = () => {
        router.get(route('laporan.surat'), localFilters, { preserveState: true, replace: true });
    };

    const resetFilters = () => {
        setLocalFilters({});
        router.get(route('laporan.surat'), {});
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Laporan Surat">
            <Head title="Laporan Surat Pengajuan - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader 
                    icon={FileText}
                    title="Laporan Surat"
                    subtitle={`Total ${totalSurat} pengajuan surat`}
                    backHref={route('laporan.index')}
                />

                {/* ── Filter Panel ── */}
                <FilterContainer 
                    title="Filter Laporan Surat" 
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
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari Nama/NIK</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300" />
                                    <input value={localFilters.search ?? ''} onChange={e => setLocalFilters(p => ({...p, search: e.target.value}))} className="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" placeholder="Ketik nama/nik..." />
                                </div>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Jenis Surat</label>
                                <select value={localFilters.jenis_surat ?? ''} onChange={e => setLocalFilters(p => ({...p, jenis_surat: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 appearance-none">
                                    <option value="">Semua Jenis</option>
                                    {(jenisSuratOptions ?? []).map(j => <option key={j} value={j}>{j}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Status</label>
                                <select value={localFilters.status ?? ''} onChange={e => setLocalFilters(p => ({...p, status: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 appearance-none">
                                    <option value="">Semua Status</option>
                                    {(statusOptions ?? []).map(s => <option key={s} value={s}>{s.toUpperCase()}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Periode</label>
                                <input type="date" value={localFilters.start_date ?? ''} onChange={e => setLocalFilters(p => ({...p, start_date: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" />
                            </div>
                        </div>
                        <button onClick={applyFilters} className="mt-4 px-6 py-2.5 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-md">Terapkan Filter</button>
                    </div>
                </FilterContainer>

                {/* ── Table ── */}
                <Deferred data="surats" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['Warga', 'Jenis Surat', 'Status', 'Tanggal Diajukan', 'Petugas'].map(h => (
                                            <th key={h} className="px-6 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {(surats?.data ?? []).length > 0 ? (surats?.data ?? []).map((s) => {
                                        const cfg = STATUS_CONFIG[s.status] ?? STATUS_CONFIG.pending;
                                        const StatusIcon = cfg.icon;
                                        return (
                                            <tr key={s.id} className="hover:bg-gray-50/50 transition-all group">
                                                <td className="px-6 py-4">
                                                    <p className="text-xs font-black text-gray-900 truncate">{s.penduduk?.nama ?? 'Umum'}</p>
                                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{s.penduduk?.nik ?? 'No NIK'}</p>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <ShieldCheck className="w-3.5 h-3.5 text-gray-300" />
                                                        <span className="text-xs font-black text-gray-700">{s.jenis_surat}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className={cn('inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border', cfg.bg, cfg.color, cfg.border)}>
                                                        <StatusIcon className="w-3 h-3" />
                                                        {s.status}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <Calendar className="w-3 h-3 text-gray-300" />
                                                        <span className="text-[10px] font-bold text-gray-500">{new Date(s.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-left">
                                                    <div className="flex items-center gap-2">
                                                        <div className="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <User className="w-3 h-3 text-gray-400" />
                                                        </div>
                                                        <span className="text-[10px] font-bold text-gray-600 truncate">{s.admin?.name ?? '—'}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    }) : (
                                        <tr><td colSpan={5} className="px-6 py-12 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest">Tidak ada pengajuan surat yang ditemukan</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {surats?.last_page > 1 && (
                            <div className="p-5 border-t border-gray-50 flex items-center justify-between bg-gray-50/30">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Halaman {surats.current_page} dari {surats.last_page}</p>
                                <div className="flex gap-1">
                                    {surats.links.map((link, i) => (
                                        link.url ? (
                                            <Link 
                                                key={i} 
                                                href={link.url} 
                                                className={cn(
                                                    "w-8 h-8 flex items-center justify-center rounded-lg text-[10px] font-black transition-all",
                                                    link.active ? "bg-rose-600 text-white shadow-lg shadow-rose-200" : "bg-white text-gray-400 hover:bg-rose-50 hover:text-rose-600 border border-gray-100"
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

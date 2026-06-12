import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { GitBranch, Search, Filter, ChevronLeft, ChevronRight as ChevronRightIcon, X, Baby, Skull, TrendingUp, TrendingDown, Users } from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { PageHeader, FilterContainer } from '@/Components/Shared';

const JENIS_BADGE = {
    kelahiran:    { color: 'bg-green-100 text-green-700',   label: 'Kelahiran'    },
    kematian:     { color: 'bg-red-100 text-red-700',       label: 'Kematian'     },
    pindah_masuk: { color: 'bg-blue-100 text-blue-700',     label: 'Pindah Masuk' },
    pindah_keluar:{ color: 'bg-orange-100 text-orange-700', label: 'Pindah Keluar'},
    pisah_kk:     { color: 'bg-purple-100 text-purple-700', label: 'Pisah KK'     },
    pindah_rt_rw: { color: 'bg-gray-100 text-gray-700',     label: 'Pindah RT/RW' },
};

export default function MutasiIndex({ auth, mutasis, stats, filters, jenisMutasiOptions }) {
    const [localFilters, setLocalFilters] = React.useState(filters ?? {});

    const applyFilters = () => router.get(route('laporan.mutasi'), localFilters, { preserveState: true, replace: true });
    const resetFilters = () => { setLocalFilters({}); router.get(route('laporan.mutasi'), {}); };

    return (
        <AuthenticatedLayout user={auth.user} title="Laporan Mutasi">
            <Head title="Laporan Mutasi - Admin Panel" />
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader 
                    icon={GitBranch}
                    title="Laporan Mutasi"
                    subtitle="Kelahiran, kematian & perpindahan"
                    backHref={route('laporan.index')}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<div className="grid grid-cols-3 lg:grid-cols-6 gap-3">{[...Array(6)].map((_,i)=><div key={i} className="h-20 bg-white rounded-xl animate-pulse border border-gray-100"/>)}</div>}>
                    <div className="grid grid-cols-3 lg:grid-cols-6 gap-3">
                        {[
                            { key:'total',label:'Total',icon:GitBranch,cls:'bg-gray-50 text-gray-600'},
                            { key:'kelahiran',label:'Kelahiran',icon:Baby,cls:'bg-green-50 text-green-600'},
                            { key:'kematian',label:'Kematian',icon:Skull,cls:'bg-red-50 text-red-600'},
                            { key:'pindah_masuk',label:'Pindah Masuk',icon:TrendingUp,cls:'bg-blue-50 text-blue-600'},
                            { key:'pindah_keluar',label:'Pindah Keluar',icon:TrendingDown,cls:'bg-orange-50 text-orange-600'},
                            { key:'pisah_kk',label:'Pisah KK',icon:Users,cls:'bg-purple-50 text-purple-600'},
                        ].map(({key,label,icon:Icon,cls}) => (
                            <div key={key} className="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                                <div className={cn('w-8 h-8 rounded-lg flex items-center justify-center mx-auto mb-2', cls)}><Icon className="w-4 h-4" /></div>
                                <p className="text-lg font-black text-gray-900 italic">{stats?.[key] ?? 0}</p>
                                <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-tight">{label}</p>
                            </div>
                        ))}
                    </div>
                </Deferred>

                {/* Filter */}
                <FilterContainer 
                    title="Filter Mutasi" 
                    subtitle="Cari dan saring data mutasi" 
                    hasActiveFilters={Object.keys(localFilters ?? {}).length > 0}
                >
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Filter</h3>
                            <button onClick={resetFilters} className="text-[9px] font-black text-red-400 uppercase tracking-widest flex items-center gap-1"><X className="w-3 h-3" />Reset</button>
                        </div>
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Nama</label>
                                <div className="relative"><Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300"/>
                                    <input value={localFilters.search??''} onChange={e=>setLocalFilters(p=>({...p,search:e.target.value}))} className="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold" placeholder="Nama..." />
                                </div>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Jenis</label>
                                <select value={localFilters.jenis_mutasi??''} onChange={e=>setLocalFilters(p=>({...p,jenis_mutasi:e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold appearance-none">
                                    <option value="">Semua</option>
                                    {(jenisMutasiOptions??[]).map(j=><option key={j} value={j}>{j.replace(/_/g,' ')}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Dari</label>
                                <input type="date" value={localFilters.start_date??''} onChange={e=>setLocalFilters(p=>({...p,start_date:e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold"/>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Sampai</label>
                                <input type="date" value={localFilters.end_date??''} onChange={e=>setLocalFilters(p=>({...p,end_date:e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold"/>
                            </div>
                        </div>
                        <button onClick={applyFilters} className="mt-4 px-6 py-2.5 bg-purple-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-purple-700 transition-all">Terapkan</button>
                    </div>
                </FilterContainer>

                {/* Table */}
                <Deferred data="mutasis" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="p-4 border-b border-gray-50">
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Menampilkan {mutasis?.from??0}–{mutasis?.to??0} dari {mutasis?.total??0}</p>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50">
                                    <tr>{['No','Nama Penduduk','Jenis Mutasi','Tanggal','Keterangan'].map(h=><th key={h} className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{h}</th>)}</tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {(mutasis?.data??[]).length > 0 ? (mutasis?.data??[]).map((m,i)=>{
                                        const badge = JENIS_BADGE[m.jenis_mutasi]??{color:'bg-gray-100 text-gray-700',label:m.jenis_mutasi};
                                        return (
                                            <tr key={m.id} className="hover:bg-gray-50/50 transition-all">
                                                <td className="px-4 py-3 text-[10px] font-bold text-gray-400">{(mutasis.from??1)+i}</td>
                                                <td className="px-4 py-3 text-xs font-black text-gray-900">{m.penduduk?.nama??'—'}</td>
                                                <td className="px-4 py-3"><span className={cn('px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest',badge.color)}>{badge.label}</span></td>
                                                <td className="px-4 py-3 text-[10px] font-bold text-gray-500">
                                                    {m.tanggal_mutasi ? new Date(m.tanggal_mutasi).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}
                                                </td>
                                                <td className="px-4 py-3 text-[10px] font-bold text-gray-500 max-w-48 truncate">{m.keterangan??'—'}</td>
                                            </tr>
                                        );
                                    }) : <tr><td colSpan={5} className="px-4 py-12 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest">Tidak ada data</td></tr>}
                                </tbody>
                            </table>
                        </div>
                        {mutasis?.last_page > 1 && (
                            <div className="p-4 border-t border-gray-50 flex items-center justify-between">
                                <p className="text-[9px] font-bold text-gray-400">Halaman {mutasis.current_page} dari {mutasis.last_page}</p>
                                <div className="flex gap-1">
                                    {mutasis.prev_page_url && <Link href={mutasis.prev_page_url} className="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-all"><ChevronLeft className="w-4 h-4"/></Link>}
                                    {mutasis.next_page_url && <Link href={mutasis.next_page_url} className="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-all"><ChevronRightIcon className="w-4 h-4"/></Link>}
                                </div>
                            </div>
                        )}
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

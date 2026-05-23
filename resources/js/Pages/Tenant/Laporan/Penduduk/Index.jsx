import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, Search, Filter, Download, X } from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { PageHeader, TableCard } from '@/Components/Shared';

export default function PendudukIndex({ auth, penduduks, totalPenduduk, filters, dusunOptions, rtOptions, jenisKelaminOptions, statusPerkawinanOptions }) {

    const [localFilters, setLocalFilters] = React.useState(filters ?? {});
    const [showFilter, setShowFilter] = React.useState(false);

    const applyFilters = () => {
        router.get(route('laporan.penduduk'), localFilters, { preserveState: true, replace: true });
    };

    const resetFilters = () => {
        setLocalFilters({});
        router.get(route('laporan.penduduk'), {});
    };

    const activeFilters = Object.values(filters ?? {}).filter(Boolean).length;

    return (
        <AuthenticatedLayout user={auth.user} title="Laporan Penduduk">
            <Head title="Laporan Penduduk - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader
                    title="Laporan Penduduk"
                    subtitle={`Total ${totalPenduduk?.toLocaleString('id-ID')} jiwa terdaftar`}
                    icon={Users}
                    backHref={route('laporan.index')}
                    actions={[
                        {
                            label: activeFilters > 0 ? `Filter (${activeFilters})` : 'Filter',
                            icon: Filter,
                            onClick: () => setShowFilter(!showFilter),
                            variant: showFilter ? 'white' : 'outline',
                        },
                        {
                            label: 'Export PDF',
                            icon: Download,
                            href: route('laporan.generate') + '?type=penduduk&format=pdf&start_date=2020-01-01&end_date=' + new Date().toISOString().slice(0, 10),
                            variant: 'white',
                        }
                    ]}
                />

                {/* ── Filter Panel ── */}
                {showFilter && (
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Filter Data</h3>
                            <button onClick={resetFilters} className="text-[9px] font-black text-red-400 uppercase tracking-widest flex items-center gap-1 hover:text-red-600 transition-all">
                                <X className="w-3 h-3" /> Reset
                            </button>
                        </div>
                        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari Nama/NIK</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300" />
                                    <input value={localFilters.search ?? ''} onChange={e => setLocalFilters(p => ({...p, search: e.target.value}))} className="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500/20" placeholder="Nama atau NIK..." />
                                </div>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Dusun</label>
                                <select value={localFilters.dusun_id ?? ''} onChange={e => setLocalFilters(p => ({...p, dusun_id: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500/20 appearance-none">
                                    <option value="">Semua Dusun</option>
                                    {(dusunOptions ?? []).map(d => <option key={d.id} value={d.id}>{d.nama}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Jenis Kelamin</label>
                                <select value={localFilters.jenis_kelamin ?? ''} onChange={e => setLocalFilters(p => ({...p, jenis_kelamin: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500/20 appearance-none">
                                    <option value="">Semua</option>
                                    {(jenisKelaminOptions ?? []).map(j => <option key={j} value={j}>{j}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Status Kawin</label>
                                <select value={localFilters.status_perkawinan ?? ''} onChange={e => setLocalFilters(p => ({...p, status_perkawinan: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500/20 appearance-none">
                                    <option value="">Semua</option>
                                    {(statusPerkawinanOptions ?? []).map(s => <option key={s} value={s}>{s}</option>)}
                                </select>
                            </div>
                        </div>
                        <div className="flex gap-2 mt-4">
                            <button onClick={applyFilters} className="px-6 py-2.5 bg-green-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md">Terapkan Filter</button>
                        </div>
                    </div>
                )}

                {/* ── Table ── */}
                <Deferred data="penduduks" fallback={<SkeletonTable columns={7} rows={10} />}>
                    <TableCard
                        icon={Users}
                        title="Data Penduduk"
                        total={penduduks?.total}
                        totalLabel="Jiwa"
                        pagination={penduduks}
                        noPadding
                    >
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['No','NIK','Nama','Jenis Kelamin','Tgl Lahir','Status Kawin','Pekerjaan'].map(h => (
                                            <th key={h} className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {(penduduks?.data ?? []).length > 0 ? (penduduks?.data ?? []).map((p, i) => (
                                        <tr key={p.id} className="hover:bg-gray-50/50 transition-all">
                                            <td className="px-4 py-3 text-[10px] font-bold text-gray-400">{(penduduks.from ?? 1) + i}</td>
                                            <td className="px-4 py-3 text-xs font-black text-gray-700 font-mono">{p.nik}</td>
                                            <td className="px-4 py-3 text-xs font-black text-gray-900">{p.nama}</td>
                                            <td className="px-4 py-3 text-[10px] font-bold text-gray-500">{p.jenis_kelamin}</td>
                                            <td className="px-4 py-3 text-[10px] font-bold text-gray-500">
                                                {p.tanggal_lahir ? new Date(p.tanggal_lahir).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}
                                            </td>
                                            <td className="px-4 py-3 text-[10px] font-bold text-gray-500">{p.status_perkawinan}</td>
                                            <td className="px-4 py-3 text-[10px] font-bold text-gray-500">{p.pekerjaan ?? '—'}</td>
                                        </tr>
                                    )) : (
                                        <tr><td colSpan={7} className="px-4 py-12 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest">Tidak ada data ditemukan</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

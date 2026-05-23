import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Deferred, router } from '@inertiajs/react';
import { 
    Users, MapPin, Search, User
} from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';

// Shared Components
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';

export default function RtDetail({ auth, rt, penduduks }) {
    const [searchTerm, setSearchTerm] = useState('');
    
    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
        router.get(
            route('settings.wilayah.detail-rt', rt.id), 
            { search: e.target.value }, 
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Detail RT ${rt.kode} - Master Wilayah`}>
            <Head title={`Detail RT ${rt.kode} - Master Wilayah`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                
                {/* ── Header ── */}
                <PageHeader 
                    title={`Detail RT ${rt.kode}`}
                    subtitle={`RW ${rt.rw?.kode || '—'} • ${rt.dusun?.nama || '—'}`}
                    icon={MapPin}
                    backHref={route('settings.wilayah.index')}
                    actions={[
                        {
                            label: `${penduduks?.total || 0} JIWA`,
                            icon: Users,
                            variant: 'white',
                            disabled: true
                        }
                    ]}
                />

                <Deferred data="penduduks" fallback={<SkeletonTable rows={10} columns={5} />}>
                    <TableCard
                        title="Daftar Penduduk"
                        icon={Users}
                        pagination={penduduks}
                        noPadding
                        filters={
                            <div className="relative w-full sm:w-72 mt-4 sm:mt-0">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Cari Nama atau NIK..."
                                    value={searchTerm}
                                    onChange={handleSearch}
                                    className="w-full pl-9 pr-4 py-2 bg-gray-50 border-transparent rounded-xl text-xs focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                />
                            </div>
                        }
                    >
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Penduduk</th>
                                        <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelahiran</th>
                                        <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kedudukan</th>
                                        <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat / NKK</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {penduduks?.data?.length > 0 ? penduduks.data.map((p) => (
                                        <tr key={p.id} className="hover:bg-gray-50/50 transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className={cn(
                                                        "w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs",
                                                        p.jenis_kelamin === 'Laki-Laki' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600'
                                                    )}>
                                                        <User className="w-4 h-4" />
                                                    </div>
                                                    <div>
                                                        <div className="text-xs font-black text-gray-900 uppercase">{p.nama}</div>
                                                        <div className="text-[10px] font-bold text-gray-500 font-mono mt-0.5">{p.nik}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-[11px] font-bold text-gray-700">{p.tempat_lahir}</div>
                                                <div className="text-[10px] text-gray-500 mt-0.5">{new Date(p.tanggal_lahir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <Badge color={p.kedudukan_keluarga === 'Kepala Keluarga' ? 'indigo' : 'gray'}>
                                                    {p.kedudukan_keluarga}
                                                </Badge>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-[11px] font-bold text-gray-700 line-clamp-1">{p.kartu_keluarga?.alamat || '—'}</div>
                                                <div className="text-[10px] text-gray-500 font-mono mt-0.5">NKK: {p.kartu_keluarga?.nkk || '—'}</div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr>
                                            <td colSpan={4}>
                                                <EmptyState 
                                                    title="Belum Ada Penduduk"
                                                    message="Tidak ada data penduduk yang terekam di RT ini."
                                                />
                                            </td>
                                        </tr>
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

import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, Deferred, router } from '@inertiajs/react';
import { 
    ChevronLeft, Users, MapPin, Search, User
} from 'lucide-react';
import { cn } from '@/lib/utils';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';

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
        <AuthenticatedLayout user={auth.user}>
            <Head title={`Detail RT ${rt.kode} - Master Wilayah`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                
                {/* ── Header ── */}
                <div className="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <Link href={route('settings.wilayah.index')} className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 text-white hover:bg-white/30 transition-all">
                                <ChevronLeft className="w-5 h-5" />
                            </Link>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Detail RT {rt.kode}</h1>
                                <p className="text-blue-100 font-bold text-[10px] uppercase tracking-widest mt-0.5 opacity-80">RW {rt.rw?.kode || '—'} • {rt.dusun?.nama || '—'}</p>
                            </div>
                        </div>
                        <div className="flex gap-2 flex-wrap items-center bg-white/10 rounded-2xl px-4 py-2 border border-white/10">
                            <Users className="w-4 h-4 text-yellow-300" />
                            <span className="text-white font-black text-sm">{penduduks?.total || 0}</span>
                            <span className="text-blue-100 text-[10px] uppercase tracking-widest font-bold">Jiwa</span>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-4 sm:p-6 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tight flex items-center gap-2">
                            <Users className="w-4 h-4 text-blue-600" /> Daftar Penduduk
                        </h2>
                        
                        <div className="relative w-full sm:w-72">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                type="text"
                                placeholder="Cari Nama atau NIK..."
                                value={searchTerm}
                                onChange={handleSearch}
                                className="w-full pl-9 pr-4 py-2 bg-gray-50 border-transparent rounded-xl text-xs focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                            />
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <Deferred data="penduduks" fallback={<SkeletonTable rows={10} columns={5} />}>
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
                                                <span className={cn(
                                                    "px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest",
                                                    p.kedudukan_keluarga === 'Kepala Keluarga' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600'
                                                )}>
                                                    {p.kedudukan_keluarga}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-[11px] font-bold text-gray-700 line-clamp-1">{p.kartu_keluarga?.alamat || '—'}</div>
                                                <div className="text-[10px] text-gray-500 font-mono mt-0.5">NKK: {p.kartu_keluarga?.nkk || '—'}</div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr>
                                            <td colSpan={4} className="px-6 py-12 text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                                                Tidak ada data penduduk di RT ini.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                            {penduduks?.data?.length > 0 && penduduks?.links && (
                                <div className="p-4 border-t border-gray-50">
                                    <Pagination links={penduduks.links} />
                                </div>
                            )}
                        </Deferred>
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}

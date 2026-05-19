import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import * as Icons from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';

export default function TrashPendudukIndex({ penduduks, filters }) {
    const [search, setSearch] = useState(filters?.search || '');

    // Handle search debounce
    useEffect(() => {
        const delayDebounceFn = setTimeout(() => {
            if (search !== (filters?.search || '')) {
                router.get(route('settings.trash.penduduk.index'), { search }, { preserveState: true, preserveScroll: true, replace: true });
            }
        }, 500);

        return () => clearTimeout(delayDebounceFn);
    }, [search]);

    const handleRestore = (id, name) => {
        Swal.fire({
            title: 'Pulihkan Data?',
            text: `Data ${name} akan dikembalikan ke daftar penduduk aktif.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Pulihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('settings.trash.penduduk.restore', id), {}, {
                    preserveScroll: true
                });
            }
        });
    };

    const handleForceDelete = (id, name) => {
        Swal.fire({
            title: 'Hapus Permanen?',
            text: `Data ${name} akan dihapus selamanya dari sistem. NIK tersebut akan bisa digunakan kembali. Tindakan ini TIDAK dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Selamanya!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('settings.trash.penduduk.force-delete', id), {
                    preserveScroll: true
                });
            }
        });
    };

    return (
        <AuthenticatedLayout title="Sampah Data Penduduk">
            <Head title="Sampah Data Penduduk" />

            <div className="space-y-6">
                {/* Header */}
                <div className="bg-gradient-to-r from-red-600 via-red-700 to-red-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Icons.Trash2 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Sampah Data Penduduk</h1>
                                <p className="text-red-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Manajemen data penduduk yang terhapus tanpa mutasi resmi
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <Link href={route('penduduk.index')} className="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl transition-all shadow-lg text-sm font-bold uppercase tracking-wider">
                                <Icons.ArrowLeft className="w-4 h-4 mr-2" />
                                Kembali ke Penduduk
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Filter & Search */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <div className="flex flex-col sm:flex-row gap-4 items-center">
                        <div className="w-full relative">
                            <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <Icons.Search className="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                placeholder="Cari NIK atau Nama penduduk yang terhapus..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-11 w-full border-gray-200 rounded-2xl focus:ring-red-500 focus:border-red-500 bg-gray-50/50 py-3"
                            />
                        </div>
                    </div>
                </div>

                {/* Data Table */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 className="text-lg font-bold text-gray-800 flex items-center uppercase italic tracking-tighter">
                            <Icons.List className="w-5 h-5 text-red-600 mr-3" />
                            Daftar Antrean Penghapusan
                        </h3>
                        {penduduks && (
                            <span className="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                                Total: {penduduks.total} Data
                            </span>
                        )}
                    </div>

                    <Deferred data="penduduks" fallback={<SkeletonTable rows={5} />}>
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-gray-50/50 text-gray-600 text-xs uppercase font-bold tracking-wider">
                                    <tr>
                                        <th className="px-6 py-4 whitespace-nowrap">Penduduk</th>
                                        <th className="px-6 py-4">Alamat & Wilayah</th>
                                        <th className="px-6 py-4 text-center whitespace-nowrap">Waktu Hapus</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100">
                                    {penduduks && penduduks.data.length > 0 ? (
                                        penduduks.data.map((p) => (
                                            <tr key={p.id} className="hover:bg-gray-50/50 transition-colors">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center">
                                                        <div className="h-10 w-10 shrink-0 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-sm">
                                                            <Icons.User className="h-5 w-5 text-white" />
                                                        </div>
                                                        <div className="ml-4">
                                                            <div className="font-bold text-gray-900">{p.nama}</div>
                                                            <div className="text-xs font-mono text-gray-500 mt-0.5">NIK: {p.nik}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="text-sm font-medium text-gray-900">{p.alamat || '-'}</div>
                                                    <div className="text-xs text-gray-500 mt-0.5">
                                                        RT {p.rt_label} / RW {p.rw_label} ({p.dusun_label})
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                                        <Icons.Calendar className="w-3.5 h-3.5 mr-1.5" />
                                                        {new Date(p.deleted_at).toLocaleString('id-ID', {
                                                            day: 'numeric',
                                                            month: 'short',
                                                            year: 'numeric',
                                                            hour: '2-digit',
                                                            minute: '2-digit'
                                                        })}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <div className="flex items-center justify-end space-x-2">
                                                        <button
                                                            onClick={() => handleRestore(p.id, p.nama)}
                                                            className="inline-flex items-center px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl transition-colors text-xs font-bold"
                                                        >
                                                            <Icons.RotateCcw className="w-3.5 h-3.5 mr-1.5" />
                                                            Pulihkan
                                                        </button>
                                                        <button
                                                            onClick={() => handleForceDelete(p.id, p.nama)}
                                                            className="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl transition-colors text-xs font-bold"
                                                        >
                                                            <Icons.Trash2 className="w-3.5 h-3.5 mr-1.5" />
                                                            Hapus Permanen
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="4" className="px-6 py-16 text-center">
                                                <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                                    <Icons.PackageOpen className="w-10 h-10 text-gray-300" />
                                                </div>
                                                <h3 className="text-sm font-bold text-gray-900">Tempat Sampah Kosong</h3>
                                                <p className="text-xs text-gray-500 mt-1">Tidak ada data penduduk terhapus yang ditemukan.</p>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        {/* Pagination */}
                        {penduduks && penduduks.data.length > 0 && (
                            <div className="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                                <Pagination links={penduduks.links} from={penduduks.from} to={penduduks.to} total={penduduks.total} />
                            </div>
                        )}
                    </Deferred>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

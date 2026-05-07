import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TestimoniStats from '@/Components/Testimoni/TestimoniStats';
import TestimoniFilters from '@/Components/Testimoni/TestimoniFilters';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { MessageSquare, Plus, Star, Edit2, Trash2, Eye, CheckCircle } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, testimonis, stats, filters }) {
    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus testimoni dari <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS DATA!',
            cancelButtonText: 'BATALKAN',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('testimoni.destroy', id), {
                    preserveScroll: true
                });
            }
        });
    };

    const handleStatusUpdate = (id, status) => {
        router.post(route('testimoni.update-status', id), { status }, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: 'Status testimoni telah diperbarui.',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            }
        });
    };

    const renderStars = (rating) => (
        <div className="flex gap-0.5">
            {[...Array(5)].map((_, i) => (
                <Star key={i} className={`w-3 h-3 ${i < rating ? 'fill-orange-400 text-orange-400' : 'text-gray-200'}`} />
            ))}
        </div>
    );

    return (
        <AuthenticatedLayout user={auth.user} title="Testimoni Warga">
            <Head title="Testimoni Warga - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 text-left">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <MessageSquare className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">Testimoni Warga</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">Kelola Suara & Kepuasan Masyarakat</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <Link 
                                href={route('testimoni.create')}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Stats - Pakai Deferred & Skeleton Shared */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <TestimoniStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <TestimoniFilters filters={filters} />

                {/* Data Table - Pakai Deferred & Skeleton Shared */}
                <Deferred data="testimonis" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                                <MessageSquare className="w-6 h-6 text-green-600" />
                                Daftar Testimoni
                            </h3>
                            <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest italic">
                                Total: {testimonis?.total || 0}
                            </span>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-gray-600">
                                <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                    <tr>
                                        <th className="px-6 py-4">Pengirim</th>
                                        <th className="px-6 py-4">Testimoni</th>
                                        <th className="px-6 py-4">Rating</th>
                                        <th className="px-6 py-4">Status</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {testimonis?.data?.length > 0 ? testimonis.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-green-50/20 transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3 text-left">
                                                    <div className="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-black text-xs shadow-sm italic">
                                                        {item.nama.charAt(0)}
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-gray-900 leading-tight">{item.nama}</p>
                                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{item.email || 'Tanpa Email'}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 max-w-xs text-left">
                                                <p className="font-medium text-gray-900 italic line-clamp-2 leading-relaxed">"{item.testimoni}"</p>
                                                <p className="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-widest">
                                                    {new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4 text-left">
                                                <div className="space-y-1">
                                                    <p className="text-xs font-black text-gray-900 leading-none">{item.rating}/5</p>
                                                    {renderStars(item.rating)}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-left">
                                                <span className={`px-3 py-1 rounded text-[9px] font-black uppercase tracking-widest ${
                                                    item.status === 'approved' ? 'bg-green-100 text-green-800' : 
                                                    item.status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'
                                                }`}>
                                                    {item.status}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end gap-2">
                                                    {item.status === 'pending' && (
                                                        <button onClick={() => handleStatusUpdate(item.id, 'approved')} className="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-colors" title="Approve">
                                                            <CheckCircle className="w-4 h-4" />
                                                        </button>
                                                    )}
                                                    <Link href={route('testimoni.show', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                    <Link href={route('testimoni.edit', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors">
                                                        <Edit2 className="w-4 h-4" />
                                                    </Link>
                                                    <button onClick={() => handleDelete(item.id, item.nama)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr>
                                            <td colSpan="5" className="px-6 py-20 text-center uppercase font-black text-gray-300 italic tracking-widest">Tidak ada data ditemukan</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                            <Pagination links={testimonis?.links} from={testimonis?.from} to={testimonis?.to} total={testimonis?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

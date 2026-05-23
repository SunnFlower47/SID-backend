import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TestimoniStats from '@/Components/Testimoni/TestimoniStats';
import TestimoniFilters from '@/Components/Testimoni/TestimoniFilters';
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';
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
        <div className="flex gap-0.5 text-left">
            {[...Array(5)].map((_, i) => (
                <Star key={i} className={`w-3 h-3 ${i < rating ? 'fill-orange-400 text-orange-400' : 'text-gray-200'}`} />
            ))}
        </div>
    );

    const getStatusColor = (status) => {
        switch(status) {
            case 'approved': return 'emerald';
            case 'pending': return 'amber';
            case 'rejected': return 'red';
            default: return 'gray';
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Testimoni Warga">
            <Head title="Testimoni Warga - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={MessageSquare}
                    title="Testimoni Warga"
                    subtitle="Kelola Suara & Kepuasan Masyarakat"
                    actions={[
                        { label: 'Tambah', icon: Plus, href: route('testimoni.create') }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <TestimoniStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <TestimoniFilters filters={filters} />

                {/* Data Table */}
                <Deferred data="testimonis" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <TableCard
                        icon={MessageSquare}
                        title="Daftar Testimoni"
                        total={testimonis?.total || 0}
                        pagination={testimonis}
                    >
                        {testimonis?.data?.length > 0 ? (
                            <table className="w-full text-left text-sm text-gray-600 text-left">
                                <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100 text-left">
                                    <tr>
                                        <th className="px-6 py-4">Pengirim</th>
                                        <th className="px-6 py-4">Testimoni</th>
                                        <th className="px-6 py-4">Rating</th>
                                        <th className="px-6 py-4">Status</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50 text-left">
                                    {testimonis.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-green-50/20 transition-colors text-left">
                                            <td className="px-6 py-4 text-left">
                                                <div className="flex items-center gap-3 text-left">
                                                    <div className="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-black text-xs shadow-sm italic text-left">
                                                        {item.nama.charAt(0)}
                                                    </div>
                                                    <div className="text-left">
                                                        <p className="font-bold text-gray-900 leading-tight text-left">{item.nama}</p>
                                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5 text-left">{item.email || 'Tanpa Email'}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 max-w-xs text-left">
                                                <p className="font-medium text-gray-900 italic line-clamp-2 leading-relaxed text-left">"{item.testimoni}"</p>
                                                <p className="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-widest text-left">
                                                    {new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4 text-left">
                                                <div className="space-y-1 text-left">
                                                    <p className="text-xs font-black text-gray-900 leading-none text-left">{item.rating}/5</p>
                                                    {renderStars(item.rating)}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-left">
                                                <Badge color={getStatusColor(item.status)}>{item.status}</Badge>
                                            </td>
                                            <td className="px-6 py-4 text-right text-left">
                                                <div className="flex justify-end gap-2 text-left">
                                                    {item.status === 'pending' && (
                                                        <button onClick={() => handleStatusUpdate(item.id, 'approved')} className="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-colors text-left" title="Approve">
                                                            <CheckCircle className="w-4 h-4 text-left" />
                                                        </button>
                                                    )}
                                                    <Link href={route('testimoni.show', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors text-left">
                                                        <Eye className="w-4 h-4 text-left" />
                                                    </Link>
                                                    <Link href={route('testimoni.edit', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors text-left">
                                                        <Edit2 className="w-4 h-4 text-left" />
                                                    </Link>
                                                    <button onClick={() => handleDelete(item.id, item.nama)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors text-left">
                                                        <Trash2 className="w-4 h-4 text-left" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : (
                            <EmptyState
                                icon={MessageSquare}
                                title="Belum Ada Testimoni"
                                message="Belum ada testimoni warga yang masuk."
                            />
                        )}
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

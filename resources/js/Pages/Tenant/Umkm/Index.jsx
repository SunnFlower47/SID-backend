import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UmkmStats from '@/Components/Umkm/UmkmStats';
import UmkmFilters from '@/Components/Umkm/UmkmFilters';
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Store, Plus, Edit2, Trash2, Eye, MapPin, CheckCircle, XCircle, Star, ShieldCheck, User } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, umkm, stats, filters, jenisOptions }) {
    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus UMKM <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('umkm.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Data UMKM telah berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Data UMKM">
            <Head title="Data UMKM - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={Store}
                    title="Data UMKM"
                    subtitle="Manajemen Usaha Mikro, Kecil & Menengah Desa"
                    actions={[
                        { label: 'Tambah UMKM', icon: Plus, href: route('umkm.create') }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <UmkmStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <UmkmFilters filters={filters} jenisOptions={jenisOptions} />

                {/* Data Table */}
                <Deferred data="umkm" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <TableCard
                        icon={Store}
                        title="Daftar UMKM Desa"
                        total={umkm?.total || 0}
                        totalLabel="UMKM"
                        pagination={umkm}
                        noPadding
                    >
                        {umkm?.data?.length > 0 ? (
                            <>
                                {/* Desktop View */}
                                <div className="hidden lg:block overflow-x-auto text-left">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-100">
                                            <tr>
                                                <th className="px-6 py-4">Usaha / Pemilik</th>
                                                <th className="px-6 py-4">Kategori</th>
                                                <th className="px-6 py-4">Wilayah</th>
                                                <th className="px-6 py-4 text-center">Status</th>
                                                <th className="px-6 py-4 text-center">Atribut</th>
                                                <th className="px-6 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {umkm.data.map((item) => (
                                                <tr key={item.id} className="hover:bg-green-50/20 transition-colors group">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                                                {Array.isArray(item.foto_usaha) && item.foto_usaha.length > 0 ? (
                                                                    <img src={`/storage/${item.foto_usaha[0]}`} alt={item.nama_usaha} className="w-full h-full object-cover" />
                                                                ) : (
                                                                    <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black italic">
                                                                        {item.nama_usaha ? item.nama_usaha.charAt(0) : 'U'}
                                                                    </div>
                                                                )}
                                                            </div>
                                                            <div>
                                                                <p className="font-bold text-gray-900 leading-tight">{item.nama_usaha}</p>
                                                                <div className="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                                                    <User className="w-3 h-3" />
                                                                    {item.nama_pemilik}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <Badge color="emerald">
                                                            {item.jenis_usaha.replace('_', ' ')}
                                                        </Badge>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                                            <MapPin className="w-3 h-3 text-red-400" />
                                                            {item.dusun?.nama || 'PUSAT'}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-center">
                                                        {item.status_usaha === 'aktif' ? (
                                                            <Badge color="green">
                                                                <CheckCircle className="w-3 h-3 mr-1 inline" /> AKTIF
                                                            </Badge>
                                                        ) : (
                                                            <Badge color="gray">
                                                                <XCircle className="w-3 h-3 mr-1 inline" /> {item.status_usaha.toUpperCase()}
                                                            </Badge>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex justify-center gap-2">
                                                            {item.is_unggulan && (
                                                                <div className="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center" title="UMKM Unggulan">
                                                                    <Star className="w-3.5 h-3.5 fill-current" />
                                                                </div>
                                                            )}
                                                            {item.is_verified && (
                                                                <div className="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center" title="Terverifikasi">
                                                                    <ShieldCheck className="w-3.5 h-3.5" />
                                                                </div>
                                                            )}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-right">
                                                        <div className="flex justify-end gap-1.5">
                                                            <Link href={route('umkm.show', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="Detail">
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            <Link href={route('umkm.edit', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-all shadow-sm border border-gray-200" title="Edit">
                                                                <Edit2 className="w-4 h-4" />
                                                            </Link>
                                                            <button onClick={() => handleDelete(item.id, item.nama_usaha)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100" title="Hapus">
                                                                <Trash2 className="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>

                                {/* Mobile View */}
                                <div className="lg:hidden p-4 space-y-4 bg-gray-50/50 text-left">
                                    {umkm.data.map((item) => (
                                        <div key={item.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-left">
                                            <div className="flex items-start gap-4 mb-4 text-left">
                                                <div className="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden shrink-0 text-left">
                                                    {Array.isArray(item.foto_usaha) && item.foto_usaha.length > 0 ? (
                                                        <img src={`/storage/${item.foto_usaha[0]}`} alt={item.nama_usaha} className="w-full h-full object-cover" />
                                                    ) : (
                                                        <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black italic">
                                                            {item.nama_usaha ? item.nama_usaha.charAt(0) : 'U'}
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="flex-1 min-w-0 text-left">
                                                    <div className="flex items-center gap-2 mb-1 text-left">
                                                        <h4 className="font-black text-gray-900 truncate uppercase italic tracking-tighter leading-none text-left">{item.nama_usaha}</h4>
                                                        {item.is_unggulan && <Star className="w-3 h-3 text-orange-500 fill-current" />}
                                                    </div>
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1.5 text-left">
                                                        <User className="w-3 h-3" />
                                                        {item.nama_pemilik}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div className="grid grid-cols-2 gap-2 mb-4 text-left">
                                                <div className="col-span-2 flex items-center gap-2 text-xs font-bold text-gray-600 text-left">
                                                    <MapPin className="w-3.5 h-3.5 text-red-400" />
                                                    {item.dusun?.nama || 'PUSAT'}
                                                </div>
                                                <Badge color="emerald" className="justify-center text-center">
                                                    {item.jenis_usaha}
                                                </Badge>
                                                {item.status_usaha === 'aktif' ? (
                                                    <Badge color="green" className="justify-center text-center">
                                                        AKTIF
                                                    </Badge>
                                                ) : (
                                                    <Badge color="gray" className="justify-center text-center">
                                                        {item.status_usaha.toUpperCase()}
                                                    </Badge>
                                                )}
                                            </div>

                                            <div className="flex gap-2 text-left">
                                                <Link href={route('umkm.show', item.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-blue-100 text-left">
                                                    DETAIL
                                                </Link>
                                                <Link href={route('umkm.edit', item.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-800 hover:text-white text-gray-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-gray-100 text-left">
                                                    EDIT
                                                </Link>
                                                <button onClick={() => handleDelete(item.id, item.nama_usaha)} className="px-4 py-2.5 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl transition-all border border-red-100 text-left">
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </>
                        ) : (
                            <EmptyState
                                icon={Store}
                                title="Belum Ada Data UMKM"
                                message="Silakan tambah data UMKM baru untuk mulai mendata potensi ekonomi desa."
                                action={{ label: 'Tambah UMKM Sekarang', icon: Plus, href: route('umkm.create') }}
                            />
                        )}
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

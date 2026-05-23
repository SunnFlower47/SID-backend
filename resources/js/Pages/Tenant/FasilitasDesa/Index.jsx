import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FasilitasDesaStats from '@/Components/FasilitasDesa/FasilitasDesaStats';
import FasilitasDesaFilters from '@/Components/FasilitasDesa/FasilitasDesaFilters';
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Building2, Plus, Edit2, Trash2, Eye, MapPin, CheckCircle, XCircle, Clock, Phone } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, fasilitas, stats, filters, jenisOptions }) {
    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus fasilitas <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
                router.delete(route('fasilitas-desa.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Data fasilitas desa telah berhasil dihapus.',
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
        <AuthenticatedLayout user={auth.user} title="Fasilitas Desa">
            <Head title="Fasilitas Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={Building2}
                    title="Fasilitas Desa"
                    subtitle="Manajemen Sarana & Prasarana Pelayanan Desa"
                    actions={[
                        { label: 'Tambah Fasilitas', icon: Plus, href: route('fasilitas-desa.create') }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <FasilitasDesaStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <FasilitasDesaFilters filters={filters} jenisOptions={jenisOptions} />

                {/* Data Table */}
                <Deferred data="fasilitas" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <TableCard
                        icon={Building2}
                        title="Daftar Fasilitas Desa"
                        total={fasilitas?.total || 0}
                        totalLabel="Fasilitas"
                        pagination={fasilitas}
                        noPadding
                    >
                        {fasilitas?.data?.length > 0 ? (
                            <>
                                {/* Desktop View */}
                                <div className="hidden lg:block overflow-x-auto">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-100">
                                            <tr>
                                                <th className="px-6 py-4">Fasilitas / Lokasi</th>
                                                <th className="px-6 py-4">Jenis</th>
                                                <th className="px-6 py-4">Informasi</th>
                                                <th className="px-6 py-4">Wilayah</th>
                                                <th className="px-6 py-4 text-center">Status</th>
                                                <th className="px-6 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {fasilitas.data.map((item) => (
                                                <tr key={item.id} className="hover:bg-green-50/20 transition-colors group">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                                                {item.foto ? (
                                                                    <img src={`/storage/${item.foto}`} alt={item.nama} className="w-full h-full object-cover" />
                                                                ) : (
                                                                    <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black italic">{item.nama.charAt(0)}</div>
                                                                )}
                                                            </div>
                                                            <div>
                                                                <p className="font-bold text-gray-900 leading-tight">{item.nama}</p>
                                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5 truncate max-w-[150px]">
                                                                    {item.alamat || '-'}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <Badge color="emerald">
                                                            {item.jenis.replace('_', ' ')}
                                                        </Badge>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="space-y-1">
                                                            {item.jam_operasional && (
                                                                <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tight">
                                                                    <Clock className="w-3 h-3 text-blue-500" />
                                                                    {item.jam_operasional}
                                                                </div>
                                                            )}
                                                            {item.kontak && (
                                                                <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tight">
                                                                    <Phone className="w-3 h-3 text-purple-500" />
                                                                    {item.kontak}
                                                                </div>
                                                            )}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                                            <MapPin className="w-3 h-3 text-red-400" />
                                                            {item.dusun?.nama || 'PUSAT'}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-center">
                                                        {item.status_aktif ? (
                                                            <Badge color="green">
                                                                <CheckCircle className="w-3 h-3 mr-1 inline" /> AKTIF
                                                            </Badge>
                                                        ) : (
                                                            <Badge color="gray">
                                                                <XCircle className="w-3 h-3 mr-1 inline" /> NONAKTIF
                                                            </Badge>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 text-right">
                                                        <div className="flex justify-end gap-1.5">
                                                            <Link href={route('fasilitas-desa.show', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="Detail">
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            <Link href={route('fasilitas-desa.edit', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-all shadow-sm border border-gray-200" title="Edit">
                                                                <Edit2 className="w-4 h-4" />
                                                            </Link>
                                                            <button onClick={() => handleDelete(item.id, item.nama)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100" title="Hapus">
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
                                <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                    {fasilitas.data.map((item) => (
                                        <div key={item.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-left">
                                            <div className="flex items-start gap-4 mb-4">
                                                <div className="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                                    {item.foto ? (
                                                        <img src={`/storage/${item.foto}`} alt={item.nama} className="w-full h-full object-cover" />
                                                    ) : (
                                                        <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black italic">{item.nama.charAt(0)}</div>
                                                    )}
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <h4 className="font-black text-gray-900 truncate uppercase italic tracking-tighter leading-none mb-2">{item.nama}</h4>
                                                    <Badge color="emerald">{item.jenis.replace('_', ' ')}</Badge>
                                                </div>
                                            </div>
                                            
                                            <div className="grid grid-cols-1 gap-2 mb-4">
                                                <div className="flex items-center gap-2 text-xs font-bold text-gray-600">
                                                    <MapPin className="w-3.5 h-3.5 text-red-400" />
                                                    {item.alamat || '-'}
                                                </div>
                                                <div className="flex items-center gap-2 text-xs font-bold text-gray-600">
                                                    <Clock className="w-3.5 h-3.5 text-blue-500" />
                                                    {item.jam_operasional || '-'}
                                                </div>
                                            </div>

                                            <div className="flex gap-2">
                                                <Link href={route('fasilitas-desa.show', item.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-blue-100">
                                                    DETAIL
                                                </Link>
                                                <Link href={route('fasilitas-desa.edit', item.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-800 hover:text-white text-gray-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-gray-100">
                                                    EDIT
                                                </Link>
                                                <button onClick={() => handleDelete(item.id, item.nama)} className="px-4 py-2.5 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl transition-all border border-red-100">
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </>
                        ) : (
                            <EmptyState
                                icon={Building2}
                                title="Belum Ada Fasilitas"
                                message="Silakan tambah fasilitas baru untuk melengkapi sarana prasarana desa."
                                action={{ label: 'Tambah Fasilitas', icon: Plus, href: route('fasilitas-desa.create') }}
                            />
                        )}
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}


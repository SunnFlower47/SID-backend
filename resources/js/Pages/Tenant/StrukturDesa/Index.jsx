import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StrukturDesaStats from '@/Components/StrukturDesa/StrukturDesaStats';
import StrukturDesaFilters from '@/Components/StrukturDesa/StrukturDesaFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Users, Plus, Edit2, Trash2, Eye, Phone, Mail, MapPin, CheckCircle, XCircle, Settings } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';

export default function Index({ auth, struktur, stats, filters, kategoriOptions }) {
    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus data <b class="text-red-600">${nama}</b> dari struktur desa?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
                router.delete(route('struktur-desa.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Data perangkat desa telah berhasil dihapus.',
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
        <AuthenticatedLayout user={auth.user} title="Struktur Desa">
            <Head title="Struktur Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                
                {/* Header */}
                <PageHeader 
                    title="Struktur Desa"
                    subtitle="Manajemen Perangkat & Aparatur Desa"
                    icon={Users}
                    actions={[
                        {
                            label: 'SETTING JABATAN',
                            icon: Settings,
                            href: route('master-jabatan.index'),
                            variant: 'outline'
                        },
                        {
                            label: 'TAMBAH PERANGKAT',
                            icon: Plus,
                            href: route('struktur-desa.create'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <StrukturDesaStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <StrukturDesaFilters filters={filters} kategoriOptions={kategoriOptions} />

                {/* Data Table */}
                <Deferred data="struktur" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <TableCard 
                        title="Daftar Perangkat Desa"
                        icon={Users}
                        total={struktur?.total || 0}
                        pagination={struktur}
                        noPadding
                    >
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-gray-600">
                                <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                    <tr>
                                        <th className="px-6 py-4">Perangkat</th>
                                        <th className="px-6 py-4">Kontak</th>
                                        <th className="px-6 py-4">Jabatan</th>
                                        <th className="px-6 py-4">Wilayah</th>
                                        <th className="px-6 py-4 text-center">Status</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {struktur?.data?.length > 0 ? struktur.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-green-50/20 transition-colors group">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                                        {item.foto ? (
                                                            <img 
                                                                src={`/storage/${item.foto}`} 
                                                                alt={item.nama} 
                                                                className="w-full h-full object-cover"
                                                            />
                                                        ) : (
                                                            <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black text-xs uppercase italic">
                                                                {item.nama.charAt(0)}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-gray-900 leading-tight">{item.nama}</p>
                                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">NIK: {item.nik || '-'}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="space-y-1">
                                                    <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tight">
                                                        <Phone className="w-3 h-3 text-green-500" />
                                                        {item.no_hp || '-'}
                                                    </div>
                                                    <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tight">
                                                        <Mail className="w-3 h-3 text-blue-500" />
                                                        {item.email || '-'}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="space-y-0.5 text-left">
                                                    <p className="text-xs font-black text-gray-900 uppercase italic tracking-tight">{item.jabatan}</p>
                                                    <span className="text-[9px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded uppercase tracking-widest border border-green-100">
                                                        {item.kategori.replace('_', ' ')}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                                    <MapPin className="w-3 h-3 text-red-400" />
                                                    {item.dusun_label || 'Pusat'}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                {item.status_aktif ? (
                                                    <Badge color="green" icon={CheckCircle}>AKTIF</Badge>
                                                ) : (
                                                    <Badge color="gray" icon={XCircle}>NONAKTIF</Badge>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link href={route('struktur-desa.show', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" title="Detail">
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                    <Link href={route('struktur-desa.edit', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors" title="Edit">
                                                        <Edit2 className="w-4 h-4" />
                                                    </Link>
                                                    <button onClick={() => handleDelete(item.id, item.nama)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors" title="Hapus">
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr>
                                            <td colSpan="6">
                                                <EmptyState 
                                                    title="Belum Ada Perangkat Desa"
                                                    message="Tambahkan perangkat atau aparatur desa melalui tombol di atas."
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

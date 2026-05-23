import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard } from '@/Components/Shared';
import KontakDesaStats from '@/Components/KontakDesa/KontakDesaStats';
import KontakDesaFilters from '@/Components/KontakDesa/KontakDesaFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Phone, Plus, Edit2, Trash2, Eye, Mail, MapPin, CheckCircle, XCircle, Users } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

// Kadang di Vite/React 19, import default terbaca sebagai object { default: ... }
const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, kontak, stats, filters, jenisOptions }) {
    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus kontak <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS KONTAK!',
            cancelButtonText: 'BATALKAN',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('kontak-desa.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Data kontak desa telah berhasil dihapus.',
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
        <AuthenticatedLayout user={auth.user} title="Kontak Desa">
            <Head title="Kontak Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader 
                    title="Kontak Desa"
                    subtitle="Manajemen Informasi Komunikasi & Kontak Desa"
                    icon={Phone}
                    actions={
                        <Link 
                            href={route('kontak-desa.create')}
                            className="flex items-center px-6 py-3 bg-green-600 text-white hover:bg-green-700 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-green-200 transition-all hover:scale-105 uppercase tracking-widest"
                        >
                            <Plus className="w-3.5 h-3.5 mr-2" />
                            TAMBAH KONTAK
                        </Link>
                    }
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <KontakDesaStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <KontakDesaFilters filters={filters} jenisOptions={jenisOptions} />

                {/* Data Table */}
                <Deferred data="kontak" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <TableCard 
                        title="Daftar Kontak Desa"
                        icon={Phone}
                        total={kontak?.total || 0}
                        noPadding
                        pagination={{
                            links: kontak?.links,
                            from: kontak?.from,
                            to: kontak?.to,
                            total: kontak?.total
                        }}
                    >

                        {kontak?.data?.length > 0 ? (
                            <>
                                {/* Desktop View */}
                                <div className="hidden lg:block overflow-x-auto">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                            <tr>
                                                <th className="px-6 py-4">Nama / Instansi</th>
                                                <th className="px-6 py-4">Kontak</th>
                                                <th className="px-6 py-4">Jenis / Jabatan</th>
                                                <th className="px-6 py-4">Wilayah</th>
                                                <th className="px-6 py-4 text-center">Status</th>
                                                <th className="px-6 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {kontak.data.map((item) => (
                                                <tr key={item.id} className="hover:bg-green-50/20 transition-colors group">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3 text-left">
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
                                                                <p className="font-bold text-gray-900 leading-tight text-left">{item.nama}</p>
                                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5 truncate max-w-[150px] text-left">
                                                                    {item.alamat || '-'}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="space-y-1 text-left">
                                                            <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tight text-left">
                                                                <Phone className="w-3 h-3 text-green-500" />
                                                                {item.no_hp || item.no_telepon || '-'}
                                                            </div>
                                                            <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tight text-left">
                                                                <Mail className="w-3 h-3 text-blue-500" />
                                                                {item.email || '-'}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="space-y-0.5 text-left">
                                                            <p className="text-xs font-black text-gray-900 uppercase italic tracking-tight text-left">{item.jabatan || '-'}</p>
                                                            <span className="text-[9px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded uppercase tracking-widest border border-green-100">
                                                                {item.jenis.replace('_', ' ')}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-left">
                                                        <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-left">
                                                            <MapPin className="w-3 h-3 text-red-400" />
                                                            {item.dusun_label || 'PUSAT'}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-center">
                                                        {item.status_aktif ? (
                                                            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                                <CheckCircle className="w-3 h-3" />
                                                                AKTIF
                                                            </div>
                                                        ) : (
                                                            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                                <XCircle className="w-3 h-3" />
                                                                OFFLINE
                                                            </div>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 text-right">
                                                        <div className="flex justify-end gap-1.5">
                                                            <Link href={route('kontak-desa.show', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm shadow-blue-100 border border-blue-100" title="Detail">
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            <Link href={route('kontak-desa.edit', item.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-all shadow-sm border border-gray-200" title="Edit">
                                                                <Edit2 className="w-4 h-4" />
                                                            </Link>
                                                            <button onClick={() => handleDelete(item.id, item.nama)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm shadow-red-100 border border-red-100" title="Hapus">
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
                                    {kontak.data.map((item) => (
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
                                                    <span className="inline-flex px-2 py-0.5 rounded bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest border border-green-100">
                                                        {item.jenis.replace('_', ' ')}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div className="grid grid-cols-1 gap-2 mb-4">
                                                <div className="flex items-center gap-2 text-xs font-bold text-gray-600">
                                                    <Phone className="w-3.5 h-3.5 text-green-500" />
                                                    {item.no_hp || item.no_telepon || '-'}
                                                </div>
                                                <div className="flex items-center gap-2 text-xs font-bold text-gray-600">
                                                    <Mail className="w-3.5 h-3.5 text-blue-500" />
                                                    {item.email || '-'}
                                                </div>
                                                <div className="flex items-center gap-2 text-xs font-bold text-gray-500">
                                                    <MapPin className="w-3.5 h-3.5 text-red-400" />
                                                    {item.dusun_label || 'PUSAT'}
                                                </div>
                                            </div>

                                            <div className="flex gap-2">
                                                <Link href={route('kontak-desa.show', item.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-blue-100">
                                                    DETAIL
                                                </Link>
                                                <Link href={route('kontak-desa.edit', item.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-800 hover:text-white text-gray-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-gray-100">
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
                            <div className="p-12 text-center">
                                <div className="w-64 h-64 mx-auto mb-4">
                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                </div>
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Belum Ada Kontak</h3>
                                <p className="text-sm text-gray-500 mt-2 max-w-xs mx-auto font-bold uppercase tracking-widest text-[10px]">
                                    Daftar kontak masih kosong. Silakan tambah kontak baru untuk memulai.
                                </p>
                                <Link 
                                    href={route('kontak-desa.create')}
                                    className="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl text-xs font-black shadow-lg shadow-green-200 hover:bg-green-700 transition-all mt-6 uppercase tracking-widest"
                                >
                                    <Plus className="w-4 h-4 mr-2" />
                                    TAMBAH KONTAK SEKARANG
                                </Link>
                            </div>
                        )}

                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

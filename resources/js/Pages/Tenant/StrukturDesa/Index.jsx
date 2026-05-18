import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StrukturDesaStats from '@/Components/StrukturDesa/StrukturDesaStats';
import StrukturDesaFilters from '@/Components/StrukturDesa/StrukturDesaFilters';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Users, Plus, Edit2, Trash2, Eye, Phone, Mail, MapPin, CheckCircle, XCircle, Settings } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4 text-left">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Users className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Struktur Desa</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Manajemen Perangkat & Aparatur Desa</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <Link 
                                href={route('master-jabatan.index')}
                                className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg"
                            >
                                <Settings className="w-3.5 h-3.5 mr-2" />
                                SETTING JABATAN
                            </Link>
                            <Link 
                                href={route('struktur-desa.create')}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH PERANGKAT
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <StrukturDesaStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <StrukturDesaFilters filters={filters} kategoriOptions={kategoriOptions} />

                {/* Data Table */}
                <Deferred data="struktur" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                                <Users className="w-6 h-6 text-green-600" />
                                Daftar Perangkat Desa
                            </h3>
                            <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest italic">
                                Total: {struktur?.total || 0}
                            </span>
                        </div>

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
                                                    <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                        <CheckCircle className="w-3 h-3" />
                                                        AKTIF
                                                    </div>
                                                ) : (
                                                    <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                        <XCircle className="w-3 h-3" />
                                                        NONAKTIF
                                                    </div>
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
                                            <td colSpan="6" className="px-6 py-12 text-center">
                                                <div className="w-48 h-48 mx-auto">
                                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                                </div>
                                                <p className="text-sm font-black text-gray-900 mt-2">Belum Ada Perangkat Desa</p>
                                                <p className="text-xs text-gray-500 mt-1">Tambahkan perangkat atau aparatur desa melalui tombol di atas.</p>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                            <Pagination links={struktur?.links} from={struktur?.from} to={struktur?.to} total={struktur?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

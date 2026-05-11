import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UmkmStats from '@/Components/Umkm/UmkmStats';
import UmkmFilters from '@/Components/Umkm/UmkmFilters';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Store, Plus, Edit2, Trash2, Eye, MapPin, CheckCircle, XCircle, Star, ShieldCheck, User } from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden text-left">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 text-left">
                        <div className="flex items-center space-x-4 text-left">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0 text-left">
                                <Store className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div className="text-left">
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">Data UMKM</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">Manajemen Usaha Mikro, Kecil & Menengah Desa</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3 text-left">
                            <Link 
                                href={route('umkm.create')}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest text-left"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH UMKM
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <UmkmStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <UmkmFilters filters={filters} jenisOptions={jenisOptions} />

                {/* Data Table */}
                <Deferred data="umkm" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white text-left">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter text-left">
                                <Store className="w-6 h-6 text-green-600" />
                                Daftar UMKM Desa
                            </h3>
                            <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest italic text-left">
                                Total: {umkm?.total || 0}
                            </span>
                        </div>

                        {umkm?.data?.length > 0 ? (
                            <>
                                {/* Desktop View */}
                                <div className="hidden lg:block overflow-x-auto text-left">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
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
                                                        <span className="text-[10px] font-black text-green-600 bg-green-50 px-2 py-1 rounded uppercase tracking-widest border border-green-100">
                                                            {item.jenis_usaha}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                                            <MapPin className="w-3 h-3 text-red-400" />
                                                            {item.dusun?.nama || 'PUSAT'}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-center">
                                                        {item.status_usaha === 'aktif' ? (
                                                            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                                <CheckCircle className="w-3 h-3" />
                                                                AKTIF
                                                            </div>
                                                        ) : (
                                                            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                                <XCircle className="w-3 h-3" />
                                                                {item.status_usaha.toUpperCase()}
                                                            </div>
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
                                                <span className="inline-flex px-2 py-0.5 rounded bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest border border-green-100 justify-center">
                                                    {item.jenis_usaha}
                                                </span>
                                                <span className={cn(
                                                    "inline-flex px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border justify-center",
                                                    item.status_usaha === 'aktif' ? "bg-green-50 text-green-600 border-green-100" : "bg-gray-50 text-gray-400 border-gray-100"
                                                )}>
                                                    {item.status_usaha}
                                                </span>
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
                            <div className="p-12 text-center">
                                <div className="w-64 h-64 mx-auto mb-4">
                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                </div>
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Belum Ada Data UMKM</h3>
                                <p className="text-sm text-gray-500 mt-2 max-w-xs mx-auto font-bold uppercase tracking-widest text-[10px]">
                                    Silakan tambah data UMKM baru untuk mulai mendata potensi ekonomi desa.
                                </p>
                                <Link 
                                    href={route('umkm.create')}
                                    className="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl text-xs font-black shadow-lg shadow-green-200 hover:bg-green-700 transition-all mt-6 uppercase tracking-widest"
                                >
                                    <Plus className="w-4 h-4 mr-2" />
                                    TAMBAH UMKM SEKARANG
                                </Link>
                            </div>
                        )}

                        <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                            <Pagination links={umkm?.links} from={umkm?.from} to={umkm?.to} total={umkm?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

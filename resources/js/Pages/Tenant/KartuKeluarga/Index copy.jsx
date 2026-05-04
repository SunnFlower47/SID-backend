import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ResidentStats from '@/Components/Penduduk/ResidentStats';
import ResidentFilters from '@/Components/Penduduk/ResidentFilters';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Users, Plus, FileSpreadsheet, Edit, Trash2, Eye, Crown, Heart, User, Briefcase, MapPin, IdCard, Loader2, Search, ChevronRight, Filter } from 'lucide-react';
import Swal from 'sweetalert2';
import axios from 'axios';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';

// Kadang di Vite/React 19, import default terbaca sebagai object { default: ... }
const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, penduduks, stats, rtList, rwList, dusunList, filters }) {
    const [isExporting, setIsExporting] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);
    const [showFilters, setShowFilters] = useState(filters.search || filters.rt || filters.rw || filters.dusun ? true : false);

    const handleExport = async () => {
        setIsExporting(true);

        try {
            const params = new URLSearchParams(window.location.search);
            params.delete('page');

            const response = await axios.get(route('penduduk.export.excel'), {
                params: Object.fromEntries(params),
                responseType: 'blob'
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Data_Penduduk_${new Date().toLocaleDateString('id-ID')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            // Show success animation
            setShowSuccess(true);
            setTimeout(() => setShowSuccess(false), 3000);
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengekspor data.', 'error');
        } finally {
            setIsExporting(false);
        }
    };

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus data ${nama}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('penduduk.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire('Terhapus!', 'Data penduduk berhasil dihapus.', 'success');
                    }
                });
            }
        });
    };

    const getKedudukanStyle = (kedudukan) => {
        const k = (kedudukan || '').toUpperCase();
        if (k === 'KEPALA KELUARGA') return { bg: 'bg-blue-100', text: 'text-blue-800', icon: <Crown className="w-3 h-3 mr-1" /> };
        if (k === 'ISTRI') return { bg: 'bg-pink-100', text: 'text-pink-800', icon: <Heart className="w-3 h-3 mr-1" /> };
        if (k === 'ANAK') return { bg: 'bg-green-100', text: 'text-green-800', icon: <User className="w-3 h-3 mr-1" /> };
        return { bg: 'bg-gray-100', text: 'text-gray-800', icon: <User className="w-3 h-3 mr-1" /> };
    };

    let currentKK = null;

    return (
        <AuthenticatedLayout user={auth.user} title="Data Penduduk">

            {/* Custom Loading Overlay */}
            {isExporting && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white rounded-3xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full mx-4 animate-in zoom-in-95 duration-300">
                        <div className="w-24 h-24">
                            <LottieComponent animationData={loadingAnimation} loop={true} />
                        </div>
                        <div className="text-center">
                            <h3 className="text-lg font-black text-gray-900">Mengekspor Data</h3>
                            <p className="text-sm text-gray-500 mt-1">Mohon tunggu, file Excel sedang disiapkan...</p>
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6 animate-in fade-in duration-500">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Users className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Data Penduduk</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">Kelola data warga Desa Cibatu</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <button
                                onClick={handleExport}
                                disabled={isExporting}
                                className="flex items-center px-4 py-3 bg-green-500/30 hover:bg-green-500/50 disabled:opacity-50 backdrop-blur-md border border-green-400/30 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all"
                            >
                                {isExporting ? (
                                    <Loader2 className="w-3.5 h-3.5 mr-2 animate-spin" />
                                ) : (
                                    <FileSpreadsheet className="w-3.5 h-3.5 mr-2" />
                                )}
                                EXCEL
                            </button>
                            <Link
                                href={route('penduduk.create')}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Statistics */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <ResidentStats stats={stats} />
                </Deferred>

                {/* Filter Toggle Button below stats */}
                <div className="flex justify-between items-center bg-white p-3 sm:p-5 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                    <div className="flex items-center gap-2 sm:gap-4">
                        <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                            <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                        </div>
                        <div>
                            <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter">Konfigurasi Data</h3>
                            <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pencarian & Filter Wilayah</p>
                        </div>
                    </div>
                    <button
                        onClick={() => setShowFilters(!showFilters)}
                        className={cn(
                            "flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95",
                            showFilters
                                ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                                : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                        )}
                    >
                        <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                        {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                    </button>
                </div>

                {/* Filters */}
                {showFilters && (
                    <div className="animate-in slide-in-from-top duration-300">
                        <ResidentFilters
                            filters={filters}
                            rtList={rtList}
                            rwList={rwList}
                            dusunList={dusunList}
                        />
                    </div>
                )}

                {/* Data Table */}
                <Deferred data="penduduks" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-2">
                                <Users className="w-5 h-5 text-green-500" />
                                Daftar Warga
                            </h3>
                            <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                                Total: {penduduks?.total || 0}
                            </span>
                        </div>

                        {penduduks?.data?.length > 0 ? (
                            <>
                                {/* Desktop Table */}
                                <div className="hidden lg:block overflow-x-auto">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                            <tr>
                                                <th className="px-6 py-4">Nama & Kedudukan</th>
                                                <th className="px-6 py-4">NIK</th>
                                                <th className="px-6 py-4">No KK</th>
                                                <th className="px-6 py-4">JK</th>
                                                <th className="px-6 py-4">Usia</th>
                                                <th className="px-6 py-4">Alamat</th>
                                                <th className="px-6 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {penduduks.data.map((p, index) => {
                                                const isNewFamily = currentKK !== p.nkk;
                                                currentKK = p.nkk;
                                                const style = getKedudukanStyle(p.kedudukan_keluarga);
                                                const isKepala = (p.kedudukan_keluarga || '').toUpperCase() === 'KEPALA KELUARGA';

                                                return (
                                                    <React.Fragment key={p.id}>
                                                        {isNewFamily && index > 0 && (
                                                            <tr><td colSpan="6"><div className="h-2 bg-gray-50/50"></div></td></tr>
                                                        )}
                                                        <tr className={`hover:bg-blue-50/30 transition-colors ${isNewFamily ? 'bg-green-50/20' : ''} ${isKepala ? 'bg-blue-50/20' : ''}`}>
                                                            <td className="px-6 py-4">
                                                                <div className="flex items-center gap-3">
                                                                    <div className={`w-10 h-10 rounded-full flex items-center justify-center shadow-sm ${isKepala ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-500'}`}>
                                                                        {isKepala ? <Crown className="w-5 h-5" /> : <User className="w-5 h-5" />}
                                                                    </div>
                                                                    <div>
                                                                        <p className="font-bold text-gray-900 leading-tight">{p.nama}</p>
                                                                        <span className={`inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-bold tracking-wider ${style.bg} ${style.text}`}>
                                                                            {style.icon} {p.kedudukan_keluarga}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td className="px-6 py-4 font-mono text-xs">{p.nik}</td>
                                                            <td className="px-6 py-4">
                                                                <div className="font-mono text-xs bg-green-50 text-green-800 px-2 py-1 rounded inline-block">{p.nkk}</div>
                                                            </td>
                                                            <td className="px-6 py-4">
                                                                <p className="font-bold text-gray-900">{p.jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P'}</p>
                                                            </td>
                                                            <td className="px-6 py-4">
                                                                <p className="font-bold text-gray-900">{p.usia} <span className="text-[10px] text-gray-400 font-medium">THN</span></p>
                                                            </td>
                                                            <td className="px-6 py-4">
                                                                <p className="font-medium text-gray-900 truncate max-w-[200px]">{p.alamat}</p>
                                                                <p className="text-xs text-gray-500">RT {p.rt_label}/RW {p.rw_label}</p>
                                                            </td>
                                                            <td className="px-6 py-4">
                                                                <div className="flex justify-end gap-2">
                                                                    <Link href={route('penduduk.show', p.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                                        <Eye className="w-4 h-4" />
                                                                    </Link>
                                                                    <Link href={route('penduduk.edit', p.id)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors">
                                                                        <Edit className="w-4 h-4" />
                                                                    </Link>
                                                                    <button onClick={() => handleDelete(p.id, p.nama)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                                                        <Trash2 className="w-4 h-4" />
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </React.Fragment>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>

                                {/* Mobile List View */}
                                <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                    {penduduks.data.map(p => (
                                        <div key={p.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                            <div className="flex items-start gap-4 mb-4">
                                                <div className="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                                    <User className="w-6 h-6" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <h4 className="font-black text-gray-900 truncate">{p.nama}</h4>
                                                    <div className="flex items-center gap-2 mt-1">
                                                        <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                                            {p.kedudukan_keluarga}
                                                        </span>
                                                        <span className="text-xs font-medium text-gray-500">{p.usia} thn</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="grid grid-cols-2 gap-3 mb-4">
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NIK</p>
                                                    <p className="text-xs font-mono font-bold text-gray-900">{p.nik}</p>
                                                </div>
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NO KK</p>
                                                    <p className="text-xs font-mono font-bold text-green-700">{p.nkk}</p>
                                                </div>
                                                <div className="col-span-2 bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">ALAMAT</p>
                                                    <p className="text-xs font-medium text-gray-900 truncate">{p.alamat}</p>
                                                    <p className="text-[10px] text-gray-500 mt-0.5">RT {p.rt_label}/RW {p.rw_label}</p>
                                                </div>
                                            </div>

                                            <div className="flex gap-2">
                                                <Link href={route('penduduk.show', p.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                    DETAIL
                                                </Link>
                                                <Link href={route('penduduk.edit', p.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                    EDIT
                                                </Link>
                                                <button onClick={() => handleDelete(p.id, p.nama)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
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
                                <h3 className="text-xl font-black text-gray-900">Belum Ada Data Warga</h3>
                                <p className="text-sm text-gray-500 mt-2 max-w-xs mx-auto">
                                    Daftar penduduk masih kosong. Silakan tambah data warga baru untuk memulai pengelolaan kependudukan.
                                </p>
                                <Link
                                    href={route('penduduk.create')}
                                    className="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-200 hover:bg-green-700 transition-all mt-6"
                                >
                                    <Plus className="w-4 h-4 mr-2" />
                                    TAMBAH WARGA SEKARANG
                                </Link>
                            </div>
                        )}

                        <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                            <Pagination links={penduduks?.links} from={penduduks?.from} to={penduduks?.to} total={penduduks?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>

            {/* Success Animation Overlay */}
            {showSuccess && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center animate-in zoom-in duration-300">
                        <div className="w-48 h-48">
                            <LottieComponent animationData={successAnimation} loop={false} />
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mt-4 uppercase italic tracking-tighter">Export Berhasil!</h3>
                        <p className="text-sm text-gray-500 font-bold uppercase tracking-widest mt-1">Data Anda sudah siap.</p>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}

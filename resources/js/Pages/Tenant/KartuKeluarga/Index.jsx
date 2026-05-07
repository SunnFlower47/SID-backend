import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KkStats from '@/Components/KartuKeluarga/KkStats';
import KkFilters from '@/Components/KartuKeluarga/KkFilters';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Home, Plus, FileSpreadsheet, Edit, Trash2, Eye, Download, Search, Filter, RefreshCw, AlertTriangle, CheckCircle, Ban, Loader2 } from 'lucide-react';
import Swal from 'sweetalert2';
import axios from 'axios';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, kartuKeluarga, stats, dusunList, rwList, rtList, filters }) {
    const [isExporting, setIsExporting] = useState(false);
    const [isSyncing, setIsSyncing] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);


    const handleExport = async () => {
        setIsExporting(true);
        try {
            const params = new URLSearchParams(window.location.search);
            const response = await axios.get(route('kk.export.excel'), {
                params: Object.fromEntries(params),
                responseType: 'blob'
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Data_Kartu_Keluarga_${new Date().toLocaleDateString('id-ID')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            setShowSuccess(true);
            setTimeout(() => setShowSuccess(false), 3000);
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengekspor data.', 'error');
        } finally {
            setIsExporting(false);
        }
    };

    const handleSync = () => {
        Swal.fire({
            title: 'Audit & Sinkronisasi Data KK?',
            text: 'Proses ini akan melakukan audit menyeluruh: mengecek anggota yang sudah meninggal/pindah, sinkronisasi NKK, serta menghitung ulang status KK bermasalah. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Ya, Jalankan Audit!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Close confirm dialog first to prevent overlapping
                Swal.close();

                setIsSyncing(true);
                router.post(route('kk.sync-summary'), {}, {
                    onFinish: () => setIsSyncing(false),
                    onSuccess: () => {
                        // Ditangani oleh flash message global di AuthenticatedLayout
                    },
                    onError: () => {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat sinkronisasi data.', 'error');
                    }
                });
            }
        });
    };

    const handleDelete = (nkk) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS KK',
            html: `Apakah Anda yakin ingin menghapus KK <b class="text-red-600">${nkk}</b>?<br><small class="text-red-500 font-bold uppercase tracking-widest text-[9px]">Peringatan: Semua anggota keluarga di dalamnya akan ikut terhapus!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS SEMUA!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('kk.destroy', nkk), {
                    onSuccess: () => {
                        // Let global handle flash
                    }
                });
            }
        });
    };

    const getStatusBadge = (kk) => {
        const status = kk.status_kk || 'normal';
        if (kk.anggota_aktif === 0) return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800"><Ban className="w-3 h-3 mr-1" /> KOSONG</span>;
        if (status === 'bermasalah') return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700"><AlertTriangle className="w-3 h-3 mr-1" /> BERMASALAH</span>;
        if (status === 'bermasalah_sementara') return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700"><RefreshCw className="w-3 h-3 mr-1" /> SEMENTARA</span>;
        if (status === 'resolved') return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">DIARSIP</span>;
        return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800"><CheckCircle className="w-3 h-3 mr-1" /> AKTIF</span>;
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Kartu Keluarga">
            <Head title="Kartu Keluarga" />

            {/* Loading Overlays */}
            {(isExporting || isSyncing) && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white rounded-3xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full mx-4 animate-in zoom-in-95 duration-300">
                        <div className="w-24 h-24">
                            <LottieComponent animationData={loadingAnimation} loop={true} />
                        </div>
                        <div className="text-center">
                            <h3 className="text-lg font-black text-gray-900">{isExporting ? 'Mengekspor Data' : 'Sinkronisasi Data'}</h3>
                            <p className="text-sm text-gray-500 mt-1">Mohon tunggu sebentar...</p>
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-5 sm:space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Home className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Kartu Keluarga</h1>
                                <p className="text-emerald-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">Manajemen Data Rumah Tangga</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <button
                                onClick={handleSync}
                                className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest"
                            >
                                <RefreshCw className={cn("w-3.5 h-3.5 mr-2", isSyncing && "animate-spin")} />
                                SYNC
                            </button>
                            <button
                                onClick={handleExport}
                                className="flex items-center px-4 py-3 bg-emerald-500/30 hover:bg-emerald-500/50 border border-emerald-400/30 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest"
                            >
                                <FileSpreadsheet className="w-3.5 h-3.5 mr-2" />
                                EXCEL
                            </button>
                            <Link
                                href={route('kk.create')}
                                className="flex items-center px-6 py-3 bg-white text-emerald-700 hover:bg-emerald-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Statistics */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <KkStats stats={stats} />
                </Deferred>

                {/* Quick Link to Bermasalah if any */}
                {stats?.bermasalah > 0 && (
                    <Link
                        href={route('kk.bermasalah.index')}
                        className="flex items-center justify-between bg-red-50 border border-red-100 p-4 rounded-2xl group hover:bg-red-100 transition-all"
                    >
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-red-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-red-200 group-hover:scale-110 transition-transform">
                                <AlertTriangle className="w-5 h-5" />
                            </div>
                            <div>
                                <h4 className="text-sm font-black text-red-900 uppercase italic">Perhatian Diperlukan!</h4>
                                <p className="text-xs font-bold text-red-600 uppercase tracking-widest opacity-80">Ada {stats.bermasalah} KK yang kehilangan kepala keluarga</p>
                            </div>
                        </div>
                        <div className="flex items-center text-red-700 font-black text-[10px] uppercase tracking-widest">
                            TANGANI SEKARANG
                            <Search className="ml-2 w-4 h-4" />
                        </div>
                    </Link>
                )}

                <KkFilters filters={filters} dusunList={dusunList} rwList={rwList} rtList={rtList} />

                {/* Data Table */}
                <Deferred data="kartuKeluarga" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50/50 to-white">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-2 uppercase italic tracking-tighter">
                                <Home className="w-5 h-5 text-emerald-600" />
                                Daftar Kartu Keluarga
                            </h3>
                            <span className="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black uppercase tracking-widest">
                                Total: {kartuKeluarga?.total || 0}
                            </span>
                        </div>

                        {kartuKeluarga?.data?.length > 0 ? (
                            <>
                                {/* Desktop Table */}
                                <div className="hidden lg:block overflow-x-auto">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                                            <tr>
                                                <th className="px-6 py-4">No KK</th>
                                                <th className="px-6 py-4">Kepala Keluarga</th>
                                                <th className="px-6 py-4">Wilayah</th>
                                                <th className="px-6 py-4">Anggota</th>
                                                <th className="px-6 py-4">Status</th>
                                                <th className="px-6 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {kartuKeluarga.data.map(kk => (
                                                <tr key={kk.id} className="hover:bg-emerald-50/30 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="font-mono text-xs font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded inline-block">{kk.nkk}</div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <p className="font-black text-gray-900 uppercase text-xs tracking-tight">{kk.nama_kepala_keluarga || 'Tidak Ada'}</p>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <p className="text-xs font-bold text-gray-700 uppercase tracking-tighter italic">Dsn. {kk.dusun_label || '-'}</p>
                                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">RT {kk.rt_label} / RW {kk.rw_label}</p>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-sm font-black text-gray-900">{kk.anggota_aktif}</span>
                                                            <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">AKTIF</span>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        {getStatusBadge(kk)}
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex justify-end gap-2">
                                                            <Link href={route('kk.show', kk.nkk)} className="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            <Link href={route('kk.edit', kk.nkk)} className="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-all">
                                                                <Edit className="w-4 h-4" />
                                                            </Link>
                                                            <button onClick={() => handleDelete(kk.nkk)} className="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                                                <Trash2 className="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>

                                {/* Mobile Card View */}
                                <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                    {kartuKeluarga.data.map(kk => (
                                        <div key={kk.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                            <div className="flex justify-between items-start mb-4">
                                                <div className="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">{kk.nkk}</div>
                                                {getStatusBadge(kk)}
                                            </div>
                                            <h4 className="font-black text-gray-900 uppercase italic mb-1">{kk.nama_kepala_keluarga || 'Tidak Ada'}</h4>
                                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Dsn. {kk.dusun_label} | RT {kk.rt_label}/RW {kk.rw_label}</p>

                                            <div className="flex items-center gap-4 mb-4">
                                                <div className="flex-1 bg-gray-50 rounded-xl p-3 text-center">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Anggota</p>
                                                    <p className="text-sm font-black text-gray-900">{kk.anggota_aktif} <span className="text-[10px] text-gray-400">AKTIF</span></p>
                                                </div>
                                            </div>

                                            <div className="flex gap-2">
                                                <Link href={route('kk.show', kk.nkk)} className="flex-1 py-3 bg-blue-50 text-blue-700 rounded-xl text-[10px] font-black text-center uppercase tracking-widest">DETAIL</Link>
                                                <Link href={route('kk.edit', kk.nkk)} className="flex-1 py-3 bg-gray-50 text-gray-700 rounded-xl text-[10px] font-black text-center uppercase tracking-widest">EDIT</Link>
                                                <button onClick={() => handleDelete(kk.nkk)} className="px-4 py-3 bg-red-50 text-red-600 rounded-xl"><Trash2 className="w-4 h-4" /></button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </>
                        ) : (
                            <div className="p-16 text-center">
                                <div className="w-56 h-56 mx-auto mb-4 opacity-80">
                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                </div>
                                <h3 className="text-xl font-black text-gray-900 uppercase italic">Data KK Kosong</h3>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-2">Gunakan tombol Tambah KK untuk memulai.</p>
                            </div>
                        )}

                        <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                            <Pagination links={kartuKeluarga?.links} from={kartuKeluarga?.from} to={kartuKeluarga?.to} total={kartuKeluarga?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>

            {/* Export Success Overlay */}
            {showSuccess && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm">
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

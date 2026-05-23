import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KkStats from '@/Components/KartuKeluarga/KkStats';
import KkFilters from '@/Components/KartuKeluarga/KkFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Home, Plus, FileSpreadsheet, Search, RefreshCw, AlertTriangle, CheckCircle, Ban } from 'lucide-react';
import Swal from 'sweetalert2';
import axios from 'axios';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';

// Shared Components
import { PageHeader, TableCard, EmptyState, ActionButtons, Badge } from '@/Components/Shared';
import { useSwalDelete } from '@/lib/useSwalDelete';

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, kartuKeluarga, stats, dusunList, rwList, rtList, filters }) {
    const [isExporting, setIsExporting] = useState(false);
    const [isSyncing, setIsSyncing] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);
    const confirmDelete = useSwalDelete();


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
        confirmDelete({
            html: `Apakah Anda yakin ingin menghapus KK <b class="text-red-600">${nkk}</b>?<br><small class="text-red-500 font-bold uppercase tracking-widest text-[9px]">Peringatan: Semua anggota keluarga di dalamnya akan ikut terhapus!</small>`,
            onConfirm: () => {
                router.delete(route('kk.destroy', nkk));
            }
        });
    };

    const getStatusBadge = (kk) => {
        const status = kk.status_kk || 'normal';
        if (kk.anggota_aktif === 0) return <Badge color="red" icon={Ban}>KOSONG</Badge>;
        if (status === 'bermasalah') return <Badge color="red" icon={AlertTriangle}>BERMASALAH</Badge>;
        if (status === 'bermasalah_sementara') return <Badge color="orange" icon={RefreshCw}>SEMENTARA</Badge>;
        if (status === 'resolved') return <Badge color="gray">DIARSIP</Badge>;
        return <Badge color="green" icon={CheckCircle}>AKTIF</Badge>;
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
                <PageHeader 
                    title="Kartu Keluarga"
                    subtitle="Manajemen Data Rumah Tangga"
                    icon={Home}
                    actions={[
                        {
                            label: 'SYNC',
                            icon: RefreshCw,
                            onClick: handleSync,
                            variant: 'ghost',
                            loading: isSyncing
                        },
                        {
                            label: 'EXCEL',
                            icon: FileSpreadsheet,
                            onClick: handleExport,
                            variant: 'ghost'
                        },
                        {
                            label: 'TAMBAH',
                            icon: Plus,
                            href: route('kk.create'),
                            variant: 'white'
                        }
                    ]}
                />

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
                    <TableCard 
                        icon={Home}
                        title="Daftar Kartu Keluarga"
                        total={kartuKeluarga?.total}
                        pagination={kartuKeluarga}
                        noPadding
                    >
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
                                                        <ActionButtons 
                                                            viewHref={route('kk.show', kk.nkk)}
                                                            editHref={route('kk.edit', kk.nkk)}
                                                            onDelete={() => handleDelete(kk.nkk)}
                                                        />
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

                                            <ActionButtons 
                                                viewHref={route('kk.show', kk.nkk)}
                                                editHref={route('kk.edit', kk.nkk)}
                                                onDelete={() => handleDelete(kk.nkk)}
                                                className="justify-start"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </>
                        ) : (
                            <EmptyState 
                                title="Data KK Kosong"
                                message="Gunakan tombol Tambah KK untuk memulai."
                            />
                        )}
                    </TableCard>
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

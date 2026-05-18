import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KeuanganFilters from '@/Components/Keuangan/KeuanganFilters';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import { BIDANG_MAP, BIDANG_COLOR } from '@/Constants/keuangan';
import { BarChart3, Plus, Edit2, Trash2, History, Wallet, ArrowLeft, TrendingUp, ArrowDownCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

const formatRupiah = (v) => {
    if (!v && v !== 0) return 'Rp 0';
    return `Rp ${Number(v).toLocaleString('id-ID')}`;
};

const JENIS_CONFIG = {
    pendapatan: { color: 'text-emerald-700', bg: 'bg-emerald-50', border: 'border-emerald-100' },
    belanja:    { color: 'text-blue-700',    bg: 'bg-blue-50',    border: 'border-blue-100'    },
    pembiayaan: { color: 'text-purple-700',  bg: 'bg-purple-50',  border: 'border-purple-100'  },
};

const STATUS_CONFIG = {
    disetujui: { color: 'text-green-700', bg: 'bg-green-50' },
    draft:     { color: 'text-yellow-700', bg: 'bg-yellow-50' },
    ditolak:   { color: 'text-red-700',   bg: 'bg-red-50'   },
};

export default function Index({ auth, filters = {}, tahunList = [], apbdes, stats, is_locked }) {

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus rekening <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tidak bisa dihapus jika ada histori pengeluaran</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px]',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('anggaran.delete-apbdes', id), { preserveScroll: true });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Data APBDes">
            <Head title="Manajemen APBDes - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <BarChart3 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Data APBDes</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Rekening Anggaran Pendapatan & Belanja Desa</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <Link href={route('transparansi-desa.index')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" /> DASHBOARD
                            </Link>
                            <Link href={route('anggaran.create-pengeluaran')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                                <ArrowDownCircle className="w-3.5 h-3.5 mr-2" /> PENGELUARAN
                            </Link>
                            {!is_locked && (
                                <Link href={route('anggaran.create-tahunan')} className="flex items-center px-4 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest">
                                    <Plus className="w-3.5 h-3.5 mr-2" /> TAMBAH REKENING
                                </Link>
                            )}
                        </div>
                    </div>
                </div>

                {/* Lock Banner */}
                {is_locked && (
                    <div className="bg-green-50 border border-green-200 rounded-2xl p-5 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-green-800 uppercase tracking-tighter italic">APBDes Telah Disahkan (Terkunci)</p>
                            <p className="text-[10px] font-bold text-green-700 uppercase tracking-wider mt-0.5">
                                APBDes untuk tahun aktif telah disahkan melalui Badan Permusyawaratan Desa. Rekening anggaran dikunci dan tidak dapat diubah kembali.
                            </p>
                        </div>
                    </div>
                )}

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {[
                            { label: 'Total Anggaran', value: formatRupiah(stats?.total_anggaran), color: 'blue', icon: Wallet },
                            { label: 'Total Realisasi', value: formatRupiah(stats?.total_realisasi), color: 'emerald', icon: TrendingUp },
                            { label: 'Rekening', value: stats?.count_total ?? 0, color: 'orange', icon: BarChart3 },
                            { label: 'Jenis Pendapatan', value: stats?.count_pendapatan ?? 0, color: 'purple', icon: ArrowDownCircle },
                        ].map((s, i) => {
                            const Icon = s.icon;
                            const colorMap = { blue: 'border-blue-100 bg-blue-50 text-blue-600', emerald: 'border-emerald-100 bg-emerald-50 text-emerald-600', orange: 'border-orange-100 bg-orange-50 text-orange-600', purple: 'border-purple-100 bg-purple-50 text-purple-600' };
                            const [borderCls, bgCls, textCls] = colorMap[s.color].split(' ');
                            return (
                                <div key={i} className={cn('bg-white rounded-2xl p-3 sm:p-4 border shadow-sm hover:shadow-md transition-all flex items-center gap-3', borderCls)}>
                                    <div className={cn('w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0', bgCls, textCls)}>
                                        <Icon className="w-4 h-4 sm:w-5 sm:h-5" />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">{s.label}</p>
                                        <h3 className="text-base sm:text-lg font-black text-gray-900 leading-none tracking-tighter italic">{s.value}</h3>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </Deferred>

                {/* Filters */}
                <KeuanganFilters filters={filters} tahunList={tahunList} routeName="transparansi-desa.apbdes" showBidang={true} />

                {/* Table */}
                <Deferred data="apbdes" fallback={<SkeletonTable columns={5} rows={8} />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="p-5 sm:p-6 border-b border-gray-50 flex items-center justify-between">
                            <h3 className="text-base font-black text-gray-900 uppercase italic tracking-tighter flex items-center gap-3">
                                <BarChart3 className="w-5 h-5 text-green-600" />
                                Daftar Rekening APBDes
                            </h3>
                            <span className="px-4 py-1.5 bg-green-50 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100">
                                {apbdes?.total ?? 0} Rekening
                            </span>
                        </div>

                        {apbdes?.data?.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-gray-50/80 border-b border-gray-100">
                                            {['Kode', 'Bidang/Kegiatan', 'Nama Rekening', 'Jenis', 'Sumber Dana', 'Anggaran / Realisasi', 'Aksi'].map(h => (
                                                <th key={h} className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">{h}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {apbdes.data.map((item) => {
                                            const jenisCfg  = JENIS_CONFIG[item.jenis]  ?? JENIS_CONFIG.belanja;
                                            const bidangCfg = BIDANG_COLOR[item.bidang] ?? {};
                                            return (
                                                <tr key={item.id} className="hover:bg-gray-50/50 transition-all group">
                                                    <td className="px-4 py-4 whitespace-nowrap">
                                                        <span className="text-[10px] font-black text-gray-500 uppercase tracking-wider font-mono">{item.kode_rekening}</span>
                                                    </td>
                                                    <td className="px-4 py-4 max-w-[140px]">
                                                        {item.bidang ? (
                                                            <span className={cn('px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest whitespace-nowrap', bidangCfg.bg, bidangCfg.text)}>
                                                                Bid. {item.bidang} — {BIDANG_MAP[item.bidang]}
                                                            </span>
                                                        ) : <span className="text-[9px] text-gray-300">—</span>}
                                                        {item.kegiatan && <p className="text-[8px] text-gray-400 font-bold mt-1 truncate">{item.kegiatan}</p>}
                                                    </td>
                                                    <td className="px-4 py-4 whitespace-nowrap">
                                                        <span className={cn('px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest', jenisCfg.bg, jenisCfg.color)}>
                                                            {item.jenis}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-4 max-w-[140px]">
                                                        <p className="text-[9px] font-bold text-gray-500 uppercase tracking-wide line-clamp-2">{item.sumber_dana?.replace(/_/g, ' ') ?? '-'}</p>
                                                    </td>
                                                    <td className="px-4 py-4 min-w-[180px]">
                                                        <AnggaranProgressBar anggaran={item.anggaran} realisasi={item.realisasi} height="h-1.5" />
                                                    </td>
                                                    <td className="px-4 py-4 whitespace-nowrap">
                                                        <div className="flex items-center gap-1">
                                                            <Link
                                                                href={route('anggaran.histori-pengeluaran', item.id)}
                                                                className="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all"
                                                                title="Histori Pengeluaran"
                                                            >
                                                                <History className="w-4 h-4" />
                                                            </Link>
                                                            {!is_locked && (
                                                                <>
                                                                    <Link
                                                                        href={route('anggaran.edit-apbdes', item.id)}
                                                                        className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all"
                                                                        title="Edit Rekening"
                                                                    >
                                                                        <Edit2 className="w-4 h-4" />
                                                                    </Link>
                                                                    <button
                                                                        onClick={() => handleDelete(item.id, item.nama_rekening)}
                                                                        className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                                                        title="Hapus Rekening"
                                                                    >
                                                                        <Trash2 className="w-4 h-4" />
                                                                    </button>
                                                                </>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="p-16 text-center">
                                <div className="w-56 h-56 mx-auto mb-4">
                                    <LottieComponent animationData={noDataAnimation} loop />
                                </div>
                                <h3 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter">Belum Ada Rekening APBDes</h3>
                                <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2 mb-8">Mulai dengan menambahkan rekening anggaran tahunan</p>
                                <Link href={route('anggaran.create-tahunan')} className="inline-flex items-center px-8 py-4 bg-green-600 text-white rounded-2xl text-xs font-black shadow-xl shadow-green-200 hover:bg-green-700 transition-all uppercase tracking-widest">
                                    <Plus className="w-4 h-4 mr-2" /> TAMBAH REKENING PERTAMA
                                </Link>
                            </div>
                        )}

                        <div className="p-5 border-t border-gray-50 bg-gray-50/30">
                            <Pagination links={apbdes?.links} from={apbdes?.from} to={apbdes?.to} total={apbdes?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

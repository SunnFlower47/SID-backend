import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KeuanganFilters from '@/Components/Keuangan/KeuanganFilters';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import { BIDANG_MAP, BIDANG_COLOR } from '@/Constants/keuangan';
import { BarChart3, Plus, Edit2, Trash2, History, Wallet, ArrowLeft, TrendingUp, ArrowDownCircle, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
import { PageHeader, TableCard, Badge } from '@/Components/Shared';

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
                <PageHeader
                    title="Data APBDes"
                    subtitle="Rekening Anggaran Pendapatan & Belanja Desa"
                    icon={BarChart3}
                    actions={[
                        {
                            label: 'DASHBOARD',
                            icon: ArrowLeft,
                            href: route('transparansi-desa.index'),
                            variant: 'ghost'
                        },
                        {
                            label: 'PENGELUARAN',
                            icon: ArrowDownCircle,
                            href: route('anggaran.create-pengeluaran'),
                            variant: 'ghost'
                        },
                        ...(!is_locked ? [{
                            label: 'TAMBAH REKENING',
                            icon: Plus,
                            href: route('anggaran.create-tahunan'),
                            variant: 'white'
                        }] : [])
                    ]}
                />

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
                    <TableCard
                        title="Daftar Rekening APBDes"
                        icon={BarChart3}
                        total={apbdes?.total ?? 0}
                        totalLabel="Rekening"
                        pagination={apbdes}
                        noPadding={true}
                    >

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
                                                    <td className="px-4 py-4 min-w-[160px]">
                                                        <p className="text-xs font-bold text-gray-900 leading-tight">{item.nama_rekening}</p>
                                                        {item.keterangan && <p className="text-[9px] text-gray-500 mt-1 line-clamp-1">{item.keterangan}</p>}
                                                    </td>
                                                    <td className="px-4 py-4 whitespace-nowrap">
                                                        <Badge
                                                            variant="custom"
                                                            customColors={jenisCfg}
                                                        >
                                                            {item.jenis}
                                                        </Badge>
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
                                                                        href={route('anggaran.rincian-apbdes', item.id)}
                                                                        className="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all"
                                                                        title="Rincian RAB"
                                                                    >
                                                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                                                        </svg>
                                                                    </Link>
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

                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KeuanganStats from '@/Components/Keuangan/KeuanganStats';
import AnggaranBarChart from '@/Components/Keuangan/AnggaranBarChart';
import ProyekDonutChart from '@/Components/Keuangan/ProyekDonutChart';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonChart from '@/Components/Shared/Skeleton/SkeletonChart';
import {
    TrendingUp, ChevronRight, BarChart3, PieChart,
    Wallet, Building2, ArrowRight, Calendar,
    CheckCircle2, Clock, AlertCircle, XCircle, FileText
} from 'lucide-react';
import { cn } from '@/lib/utils';

const formatRupiah = (v) => {
    if (!v && v !== 0) return 'Rp 0';
    return `Rp ${Number(v).toLocaleString('id-ID')}`;
};

const JENIS_CONFIG = {
    pendapatan: { color: 'text-emerald-600', bg: 'bg-emerald-50', border: 'border-emerald-100' },
    belanja:    { color: 'text-blue-600',    bg: 'bg-blue-50',    border: 'border-blue-100'    },
    pembiayaan: { color: 'text-purple-600',  bg: 'bg-purple-50',  border: 'border-purple-100'  },
};

const STATUS_CONFIG = {
    perencanaan: { icon: Clock,        color: 'text-blue-600',  bg: 'bg-blue-50'    },
    pelaksanaan:  { icon: AlertCircle,  color: 'text-yellow-600', bg: 'bg-yellow-50' },
    selesai:      { icon: CheckCircle2, color: 'text-green-600',  bg: 'bg-green-50'  },
    tertunda:     { icon: Clock,        color: 'text-gray-500',   bg: 'bg-gray-50'   },
    dibatalkan:   { icon: XCircle,      color: 'text-red-600',    bg: 'bg-red-50'    },
};

export default function Dashboard({ auth, tahun, tahunList = [], stats, apbdesByJenis, proyekByStatus, recentApbdes, recentProyek }) {

    const handleTahunChange = (e) => {
        router.get(route('transparansi-desa.index'), { tahun: e.target.value }, {
            preserveState: true, preserveScroll: true, replace: true,
        });
    };

    const pctSerap = stats?.total_anggaran > 0
        ? Math.min(100, Math.round((stats.total_realisasi / stats.total_anggaran) * 100))
        : 0;

    return (
        <AuthenticatedLayout user={auth.user} title="Keuangan Desa">
            <Head title="Dashboard Keuangan Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ──────────────────────────────── */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <TrendingUp className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Keuangan Desa</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Transparansi APBDes & Realisasi Proyek</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            {/* Year filter */}
                            <select
                                value={tahun}
                                onChange={handleTahunChange}
                                className="px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-black uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg focus:outline-none focus:ring-2 focus:ring-white/30 appearance-none cursor-pointer"
                            >
                                {(tahunList.length ? tahunList : [new Date().getFullYear()]).map((t) => (
                                    <option key={t} value={t} className="text-gray-900 bg-white">{t}</option>
                                ))}
                            </select>
                            <Link href={route('transparansi-desa.apbdes')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg">
                                <BarChart3 className="w-3.5 h-3.5 mr-2" />
                                APBDes
                            </Link>
                            <Link href={route('transparansi-desa.proyek')} className="flex items-center px-4 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest">
                                <Building2 className="w-3.5 h-3.5 mr-2" />
                                Proyek
                            </Link>
                            <Link href={route('laporan-keuangan.index')} className="flex items-center px-4 py-3 bg-yellow-300 text-yellow-900 hover:bg-yellow-200 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest">
                                <FileText className="w-3.5 h-3.5 mr-2" />
                                Laporan
                            </Link>
                        </div>
                    </div>
                </div>

                {/* ── Stats Cards ─────────────────────────── */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <KeuanganStats stats={stats} />
                </Deferred>

                {/* ── Serap Progress Bar ──────────────────── */}
                <Deferred data="stats" fallback={<div className="h-16 bg-white rounded-2xl animate-pulse" />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                        <div className="flex items-center justify-between mb-3">
                            <div>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Penyerapan Anggaran {tahun}</p>
                                <p className="text-lg font-black text-gray-900 italic">{pctSerap}% Terserap</p>
                            </div>
                            <div className="text-right">
                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Realisasi</p>
                                <p className="text-sm font-black text-green-600">{formatRupiah(stats?.total_realisasi)}</p>
                                <p className="text-[9px] text-gray-400 font-bold">dari {formatRupiah(stats?.total_anggaran)}</p>
                            </div>
                        </div>
                        <div className="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                className={cn(
                                    'h-full rounded-full transition-all duration-1000',
                                    pctSerap >= 80 ? 'bg-green-500' : pctSerap >= 50 ? 'bg-yellow-400' : 'bg-blue-400'
                                )}
                                style={{ width: `${pctSerap}%` }}
                            />
                        </div>
                    </div>
                </Deferred>

                {/* ── Charts ──────────────────────────────── */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {/* Bar Chart – Anggaran vs Realisasi */}
                    <Deferred data="apbdesByJenis" fallback={<SkeletonChart height="320px" />}>
                        <div className="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Anggaran vs Realisasi</h3>
                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Per Jenis APBDes Tahun {tahun}</p>
                                </div>
                                <div className="w-8 h-8 bg-green-50 rounded-xl flex items-center justify-center">
                                    <BarChart3 className="w-4 h-4 text-green-600" />
                                </div>
                            </div>
                            <AnggaranBarChart data={apbdesByJenis ?? []} />
                        </div>
                    </Deferred>

                    {/* Donut Chart – Proyek by Status */}
                    <Deferred data="proyekByStatus" fallback={<SkeletonChart height="320px" />}>
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Status Proyek</h3>
                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Distribusi Per Status</p>
                                </div>
                                <div className="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center">
                                    <PieChart className="w-4 h-4 text-blue-600" />
                                </div>
                            </div>
                            <ProyekDonutChart data={proyekByStatus ?? []} />
                        </div>
                    </Deferred>
                </div>

                {/* ── APBDes by Jenis Summary ─────────────── */}
                <Deferred data="apbdesByJenis" fallback={<div className="h-32 bg-white rounded-2xl animate-pulse" />}>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {['pendapatan', 'belanja', 'pembiayaan'].map((jenis) => {
                            const d = (apbdesByJenis ?? []).find((x) => x.jenis === jenis);
                            const cfg = JENIS_CONFIG[jenis];
                            const pct = d?.total_anggaran > 0 ? Math.min(100, Math.round((d.total_realisasi / d.total_anggaran) * 100)) : 0;
                            return (
                                <div key={jenis} className={cn('bg-white rounded-2xl border shadow-sm p-5', cfg.border)}>
                                    <div className={cn('inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest mb-3', cfg.bg, cfg.color)}>
                                        {jenis}
                                    </div>
                                    <p className="text-lg font-black text-gray-900 italic tracking-tighter">{formatRupiah(d?.total_anggaran ?? 0)}</p>
                                    <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5 mb-2">Total Anggaran</p>
                                    <div className="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div className={cn('h-full rounded-full', cfg.bg.replace('50', '500') )} style={{ width: `${pct}%`, backgroundColor: jenis === 'pendapatan' ? '#10b981' : jenis === 'belanja' ? '#3b82f6' : '#8b5cf6' }} />
                                    </div>
                                    <p className="text-[9px] text-gray-400 font-bold mt-1 uppercase tracking-widest">{pct}% direalisasi</p>
                                </div>
                            );
                        })}
                    </div>
                </Deferred>

                {/* ── Recent Tables ────────────────────────── */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {/* Recent APBDes */}
                    <Deferred data="recentApbdes" fallback={<div className="h-64 bg-white rounded-2xl animate-pulse" />}>
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="p-5 border-b border-gray-50 flex items-center justify-between">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Rekening APBDes Terbaru</h3>
                                <Link href={route('transparansi-desa.apbdes')} className="text-[9px] font-black text-green-600 uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all">
                                    Lihat Semua <ChevronRight className="w-3 h-3" />
                                </Link>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {(recentApbdes ?? []).length > 0 ? (recentApbdes ?? []).map((item) => {
                                    const cfg = JENIS_CONFIG[item.jenis] ?? JENIS_CONFIG.belanja;
                                    const pct = item.anggaran > 0 ? Math.min(100, Math.round((item.realisasi / item.anggaran) * 100)) : 0;
                                    return (
                                        <div key={item.id} className="p-4 hover:bg-gray-50/50 transition-all">
                                            <div className="flex items-start justify-between gap-2 mb-2">
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-[10px] font-black text-gray-500 uppercase tracking-widest">{item.kode_rekening}</p>
                                                    <p className="text-xs font-black text-gray-900 truncate">{item.nama_rekening}</p>
                                                </div>
                                                <span className={cn('shrink-0 px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest', cfg.bg, cfg.color)}>{item.jenis}</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <div className="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                    <div className="h-full bg-green-500 rounded-full" style={{ width: `${pct}%` }} />
                                                </div>
                                                <span className="text-[9px] font-black text-gray-400 shrink-0">{pct}%</span>
                                            </div>
                                            <p className="text-[9px] text-gray-400 font-bold mt-1">{formatRupiah(item.anggaran)}</p>
                                        </div>
                                    );
                                }) : (
                                    <div className="p-8 text-center text-gray-300">
                                        <p className="text-[10px] font-black uppercase tracking-widest">Belum ada data APBDes</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </Deferred>

                    {/* Recent Proyek */}
                    <Deferred data="recentProyek" fallback={<div className="h-64 bg-white rounded-2xl animate-pulse" />}>
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="p-5 border-b border-gray-50 flex items-center justify-between">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Proyek Desa Terbaru</h3>
                                <Link href={route('transparansi-desa.proyek')} className="text-[9px] font-black text-green-600 uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all">
                                    Lihat Semua <ChevronRight className="w-3 h-3" />
                                </Link>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {(recentProyek ?? []).length > 0 ? (recentProyek ?? []).map((proyek) => {
                                    const cfg = STATUS_CONFIG[proyek.status] ?? STATUS_CONFIG.perencanaan;
                                    const StatusIcon = cfg.icon;
                                    const pct = proyek.anggaran > 0 ? Math.min(100, Math.round((proyek.realisasi / proyek.anggaran) * 100)) : 0;
                                    return (
                                        <div key={proyek.id} className="p-4 hover:bg-gray-50/50 transition-all">
                                            <div className="flex items-start justify-between gap-2 mb-2">
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-xs font-black text-gray-900 truncate">{proyek.nama_proyek}</p>
                                                    <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{proyek.lokasi}</p>
                                                </div>
                                                <div className={cn('shrink-0 flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest', cfg.bg, cfg.color)}>
                                                    <StatusIcon className="w-2.5 h-2.5" />
                                                    {proyek.status}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <div className="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                    <div className="h-full bg-blue-500 rounded-full" style={{ width: `${pct}%` }} />
                                                </div>
                                                <span className="text-[9px] font-black text-gray-400 shrink-0">{pct}%</span>
                                            </div>
                                            <p className="text-[9px] text-gray-400 font-bold mt-1">{formatRupiah(proyek.anggaran)}</p>
                                        </div>
                                    );
                                }) : (
                                    <div className="p-8 text-center text-gray-300">
                                        <p className="text-[10px] font-black uppercase tracking-widest">Belum ada proyek terdaftar</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </Deferred>
                </div>

                {/* ── Quick Actions ────────────────────────── */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    {[
                        { label: 'Tambah Anggaran',  desc: 'Input rekening APBDes baru',      href: route('anggaran.create-tahunan'),       color: 'green'  },
                        { label: 'Catat Pengeluaran',desc: 'Realisasi rekening anggaran',      href: route('anggaran.create-pengeluaran'),   color: 'blue'   },
                        { label: 'Buat Proyek',      desc: 'Tambah proyek desa baru',          href: route('anggaran.create-proyek'),        color: 'orange' },
                        { label: 'Persetujuan BPD',  desc: 'Pengesahan Perdes APBDes',         href: route('peraturan-desa.index'),          color: 'teal'   },
                        { label: 'Cetak Laporan',    desc: 'PDF Realisasi & Buku Kas',         href: route('laporan-keuangan.index'),        color: 'purple' },
                    ].map((action) => (
                        <Link key={action.label} href={action.href}
                            className="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-[0.98] transition-all group"
                        >
                            <div>
                                <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{action.label}</p>
                                <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{action.desc}</p>
                            </div>
                            <ArrowRight className="w-4 h-4 text-gray-300 group-hover:text-green-500 group-hover:translate-x-1 transition-all" />
                        </Link>
                    ))}
                </div>

            </div>
        </AuthenticatedLayout>
    );
}

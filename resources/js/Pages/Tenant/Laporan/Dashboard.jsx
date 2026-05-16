import React from 'react';
import { Head, Link, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    BarChart3, Users, Home, RefreshCw, FileText, Newspaper,
    ArrowRight, TrendingUp, TrendingDown, Activity, ChevronRight,
    Calendar, GitBranch
} from 'lucide-react';
import { cn } from '@/lib/utils';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonActivity from '@/Components/Shared/Skeleton/SkeletonActivity';

const StatCard = ({ icon: Icon, label, value, sub, color = 'green', trend }) => {
    const colors = {
        green:  { bg: 'bg-green-50',  icon: 'text-green-600',  border: 'border-green-100' },
        blue:   { bg: 'bg-blue-50',   icon: 'text-blue-600',   border: 'border-blue-100'  },
        purple: { bg: 'bg-purple-50', icon: 'text-purple-600', border: 'border-purple-100'},
        orange: { bg: 'bg-orange-50', icon: 'text-orange-600', border: 'border-orange-100'},
        rose:   { bg: 'bg-rose-50',   icon: 'text-rose-600',   border: 'border-rose-100'  },
    };
    const c = colors[color] ?? colors.green;
    return (
        <div className={cn('bg-white rounded-2xl border shadow-sm p-5', c.border)}>
            <div className="flex items-start justify-between">
                <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center', c.bg)}>
                    <Icon className={cn('w-5 h-5', c.icon)} />
                </div>
                {trend != null && (
                    <span className={cn('text-[9px] font-black uppercase tracking-widest flex items-center gap-0.5', trend > 0 ? 'text-green-500' : trend < 0 ? 'text-red-400' : 'text-gray-400')}>
                        {trend > 0 ? <TrendingUp className="w-3 h-3"/> : trend < 0 ? <TrendingDown className="w-3 h-3"/> : null}
                        {trend !== 0 ? `${Math.abs(trend)} hari ini` : 'Tidak ada hari ini'}
                    </span>
                )}
            </div>
            <p className="text-2xl font-black text-gray-900 italic tracking-tighter mt-3">{value ?? '—'}</p>
            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">{label}</p>
            {sub && <p className="text-[9px] text-gray-400 font-bold mt-0.5">{sub}</p>}
        </div>
    );
};

const MENU_ITEMS = [
    { label: 'Laporan Penduduk',  desc: 'Data lengkap warga + filter lanjutan', route: 'laporan.penduduk', icon: Users,      color: 'green'  },
    { label: 'Laporan KK',        desc: 'Rekap kartu keluarga per wilayah',      route: 'laporan.kk',       icon: Home,       color: 'blue'   },
    { label: 'Laporan Mutasi',    desc: 'Kelahiran, kematian, pindah, pisah KK', route: 'laporan.mutasi',   icon: GitBranch,  color: 'purple' },
    { label: 'Laporan Berita',    desc: 'Rekap konten & publikasi desa',          route: 'laporan.berita',   icon: Newspaper,  color: 'orange' },
    { label: 'Laporan Surat',     desc: 'Pengajuan surat oleh warga',            route: 'laporan.surat',    icon: FileText,   color: 'rose'   },
    { label: 'Analisis Statistik',desc: 'Chart demografi & distribusi penduduk', route: 'statistics.index', icon: BarChart3,  color: 'blue'   },
    { label: 'Komparasi Data',    desc: 'Perbandingan antar periode / bulan',     route: 'comparison.index', icon: Activity,   color: 'purple' },
];

export default function Dashboard({ auth, stats, recentMutasi }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Laporan & Analisis">
            <Head title="Laporan & Analisis - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <BarChart3 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Laporan & Analisis</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Pusat Data & Statistik Desa</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link href={route('statistics.index')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest backdrop-blur-md border border-white/10 transition-all hover:scale-105">
                                <BarChart3 className="w-3.5 h-3.5 mr-2" />Statistik
                            </Link>
                            <Link href={route('comparison.index')} className="flex items-center px-4 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all hover:scale-105">
                                <Activity className="w-3.5 h-3.5 mr-2" />Komparasi
                            </Link>
                        </div>
                    </div>
                </div>

                {/* ── Stat Cards ── */}
                <Deferred data="stats" fallback={<SkeletonStats count={4} />}>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <StatCard icon={Users}    label="Total Penduduk" value={stats?.total_penduduk?.toLocaleString('id-ID')} color="green"  trend={stats?.penduduk_hari_ini} />
                        <StatCard icon={Home}     label="Kartu Keluarga" value={stats?.total_kk?.toLocaleString('id-ID')}       color="blue"   />
                        <StatCard icon={GitBranch}label="Total Mutasi"   value={stats?.total_mutasi?.toLocaleString('id-ID')}   color="purple" trend={stats?.mutasi_hari_ini} />
                        <StatCard icon={FileText} label="Surat Diajukan" value={stats?.total_surat?.toLocaleString('id-ID')}   color="orange" trend={stats?.surat_hari_ini} />
                    </div>
                </Deferred>

                {/* ── Menu Grid ── */}
                <div>
                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Pilih Jenis Laporan</p>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        {MENU_ITEMS.map((item) => {
                            const Icon = item.icon;
                            const colors = {
                                green:  'bg-green-50 text-green-600',
                                blue:   'bg-blue-50 text-blue-600',
                                purple: 'bg-purple-50 text-purple-600',
                                orange: 'bg-orange-50 text-orange-600',
                                rose:   'bg-rose-50 text-rose-600',
                            };
                            return (
                                <Link key={item.route} href={route(item.route)}
                                    className="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-[0.98] transition-all group"
                                >
                                    <div className="flex items-center gap-3">
                                        <div className={cn('w-9 h-9 rounded-xl flex items-center justify-center shrink-0', colors[item.color])}>
                                            <Icon className="w-4 h-4" />
                                        </div>
                                        <div>
                                            <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{item.label}</p>
                                            <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5 leading-tight">{item.desc}</p>
                                        </div>
                                    </div>
                                    <ArrowRight className="w-4 h-4 text-gray-300 group-hover:text-green-500 group-hover:translate-x-1 transition-all shrink-0" />
                                </Link>
                            );
                        })}
                    </div>
                </div>

                {/* ── Recent Mutasi ── */}
                <Deferred data="recentMutasi" fallback={<SkeletonActivity count={3} />}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="p-5 border-b border-gray-50 flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Mutasi Terbaru</h3>
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">5 perubahan data terakhir</p>
                            </div>
                            <Link href={route('laporan.mutasi')} className="text-[9px] font-black text-green-600 uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all">
                                Lihat Semua <ChevronRight className="w-3 h-3" />
                            </Link>
                        </div>
                        <div className="divide-y divide-gray-50">
                            {(recentMutasi ?? []).length > 0 ? (recentMutasi ?? []).map((m) => (
                                <div key={m.id} className="p-4 flex items-center justify-between hover:bg-gray-50/50 transition-all">
                                    <div>
                                        <p className="text-xs font-black text-gray-900">{m.penduduk?.nama ?? '—'}</p>
                                        <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{m.jenis_mutasi?.replace(/_/g, ' ')}</p>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-[9px] text-gray-400 font-bold">{m.tanggal_mutasi ?? m.created_at?.substring(0,10)}</p>
                                    </div>
                                </div>
                            )) : (
                                <div className="p-8 text-center text-gray-300">
                                    <p className="text-[10px] font-black uppercase tracking-widest">Belum ada data mutasi</p>
                                </div>
                            )}
                        </div>
                    </div>
                </Deferred>

            </div>
        </AuthenticatedLayout>
    );
}

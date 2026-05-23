import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import { PageHeader, StatCard } from '@/Components/Shared';
import {
    Users,
    UserCheck,
    ArrowUpRight,
    TrendingUp,
    MapPin,
    Activity,
    History,
    Plus,
    FileText,
    Repeat,
    Search,
    AlertTriangle,
    ChevronRight,
    Clock
} from 'lucide-react';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
    PieChart,
    Pie,
    Cell,
    AreaChart,
    Area
} from 'recharts';
import { cn } from '@/lib/utils';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonChart from '@/Components/Shared/Skeleton/SkeletonChart';
import SkeletonActivity from '@/Components/Shared/Skeleton/SkeletonActivity';

const COLORS = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];

export default function Dashboard({ auth, stats, mutasiStats, suratStats, recentMutasi, ageGroups, mutationTrends }) {

    const handleRefresh = () => {
        router.post(route('dashboard.refresh'));
    };

    return (
        <AuthenticatedLayout
            auth={auth}
            title="Dashboard"
        >
            <Head title="Pusat Kendali Desa" />

            <div className="space-y-6 md:space-y-10 animate-in fade-in duration-700 pb-10 text-left">
                {/* Header Section */}
                <PageHeader 
                    title="PUSAT KENDALI DESA"
                    subtitle="Sistem Informasi Desa Cibatu • Monitor & Eksekusi"
                    icon={TrendingUp}
                    actions={[{
                        label: 'REFRESH',
                        icon: History,
                        onClick: handleRefresh,
                        variant: 'ghost'
                    }]}
                >
                    <div className="flex flex-col gap-1 items-start">
                        <div className="flex items-center gap-2 -mt-1 sm:-mt-2 mb-2 sm:mb-0">
                            <span className="px-2 py-0.5 bg-green-500/30 backdrop-blur-md border border-green-400/30 text-green-100 text-[9px] font-black rounded-full uppercase tracking-widest italic">Live Monitor</span>
                            <span className="text-[9px] text-green-100/60 font-bold uppercase tracking-widest flex items-center gap-1">
                                <Clock className="w-3 h-3" />
                                {new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                            </span>
                        </div>
                    </div>
                </PageHeader>

                {/* Quick Access Section */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <QuickActionCard
                        title="Tambah Penduduk"
                        desc="Input warga baru"
                        href={route('penduduk.index')}
                        icon={<Plus />}
                        color="green"
                    />
                    <QuickActionCard
                        title="Layanan Surat"
                        desc="Cetak surat"
                        href={route('admin.surat-pengajuan.index')}
                        icon={<FileText />}
                        color="blue"
                    />
                    <QuickActionCard
                        title="Mutasi Warga"
                        desc="Lapor mutasi"
                        href={route('mutasi.data.index')}
                        icon={<Repeat />}
                        color="orange"
                    />
                    <QuickActionCard
                        title="Manajemen KK"
                        desc="Kelola data KK"
                        href={route('kk.index')}
                        icon={<Search />}
                        color="purple"
                    />
                </div>

                {/* Actionable Alerts */}
                <Deferred data="suratStats" fallback={<div className="h-16 w-full bg-gray-50 rounded-[24px] animate-pulse mb-6"></div>}>
                    <div className="space-y-3 md:space-y-4">
                        {suratStats?.pending > 0 && (
                            <Link
                                href={route('admin.surat-pengajuan.index')}
                                style={{ borderRadius: '24px' }}
                                className="flex items-center justify-between p-4 md:p-5 bg-blue-50 border border-blue-100 group hover:bg-blue-100 transition-all shadow-sm overflow-hidden"
                            >
                                <div className="flex items-center gap-3 md:gap-4">
                                    <div className="p-2.5 bg-blue-600 rounded-xl text-white shadow-lg shadow-blue-200">
                                        <FileText className="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h4 className="text-xs md:text-sm font-black text-blue-900 tracking-tight uppercase">{suratStats.pending} SURAT PENDING</h4>
                                        <p className="text-[9px] md:text-xs text-blue-700 font-bold uppercase tracking-tight opacity-70">Warga menunggu penyelesaian berkas admin.</p>
                                    </div>
                                </div>
                                <ChevronRight className="w-4 h-4 text-blue-600 group-hover:translate-x-2 transition-all" />
                            </Link>
                        )}
                    </div>
                </Deferred>

                {/* Stats Overview */}
                <Deferred data={["stats", "suratStats", "mutasiStats"]} fallback={<SkeletonStats />}>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <StatCard
                            label="Total Penduduk"
                            value={stats?.total_penduduk}
                            icon={Users}
                            badge="Live"
                            color="green"
                        />
                        <StatCard
                            label="Total KK"
                            value={stats?.total_kk}
                            icon={UserCheck}
                            badge="Aktif"
                            color="blue"
                        />
                        <StatCard
                            label="Surat Selesai"
                            value={suratStats?.selesai}
                            icon={FileText}
                            badge="Total"
                            color="purple"
                        />
                        <StatCard
                            label="Mutasi"
                            value={(mutasiStats?.kelahiran || 0) + (mutasiStats?.kematian || 0) + (mutasiStats?.pindah_masuk || 0) + (mutasiStats?.pindah_keluar || 0)}
                            icon={Activity}
                            badge="Hari ini"
                            color="orange"
                        />
                    </div>
                </Deferred>

                {/* Charts Section */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Gender Distribution */}
                    <Deferred data="stats" fallback={<SkeletonChart height="350px" />}>
                        <div
                            style={{ borderRadius: '24px' }}
                            className="bg-white p-6 border border-gray-100 shadow-sm flex flex-col items-center min-h-[350px] overflow-hidden"
                        >
                            <h3 className="text-sm font-black text-gray-950 mb-4 self-start uppercase tracking-tighter italic">Komposisi Gender</h3>
                            <div className="flex-1 w-full" style={{ minHeight: '200px' }}>
                                <ResponsiveContainer width="100%" height="100%">
                                    <PieChart>
                                        <Pie
                                            data={[
                                                { name: 'Laki-laki', value: stats?.laki_laki || 0 },
                                                { name: 'Perempuan', value: stats?.perempuan || 0 },
                                            ]}
                                            cx="50%"
                                            cy="50%"
                                            innerRadius={50}
                                            outerRadius={70}
                                            paddingAngle={5}
                                            dataKey="value"
                                        >
                                            {[0, 1].map((entry, index) => (
                                                <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                            ))}
                                        </Pie>
                                        <Tooltip
                                            contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }}
                                        />
                                    </PieChart>
                                </ResponsiveContainer>
                            </div>
                            <div className="flex flex-col gap-1.5 mt-3 w-full">
                                {[
                                    { name: 'Laki-laki', value: stats?.laki_laki || 0 },
                                    { name: 'Perempuan', value: stats?.perempuan || 0 },
                                ].map((item, i) => (
                                    <div key={item.name} className="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100/50">
                                        <div className="flex items-center gap-2">
                                            <div className="w-2.5 h-2.5 rounded-full" style={{ backgroundColor: COLORS[i] }} />
                                            <span className="text-[10px] font-black text-gray-700 uppercase tracking-tight">{item.name}</span>
                                        </div>
                                        <span className="text-xs font-black text-gray-950">{item.value.toLocaleString('id-ID')}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </Deferred>

                    {/* Age Groups Chart */}
                    <Deferred data="ageGroups" fallback={<SkeletonChart height="350px" />}>
                        <div
                            style={{ borderRadius: '24px' }}
                            className="lg:col-span-2 bg-white p-6 border border-gray-100 shadow-sm min-h-[350px] overflow-hidden"
                        >
                            <h3 className="text-sm font-black text-gray-950 mb-4 uppercase tracking-tighter italic">Demografi Usia</h3>
                            <div className="w-full" style={{ height: '250px' }}>
                                <ResponsiveContainer width="100%" height="100%">
                                    <BarChart data={[
                                        { name: 'Balita (0-5)', total: ageGroups?.balita || 0 },
                                        { name: 'Anak (6-12)', total: ageGroups?.anak || 0 },
                                        { name: 'Remaja (13-17)', total: ageGroups?.remaja || 0 },
                                        { name: 'Dewasa (18-59)', total: ageGroups?.dewasa || 0 },
                                        { name: 'Lansia (60+)', total: ageGroups?.lansia || 0 },
                                    ]}>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                                        <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 9, fontWeight: 800, fill: '#6b7280' }} dy={10} />
                                        <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 9, fontWeight: 800, fill: '#6b7280' }} />
                                        <Tooltip
                                            cursor={{ fill: '#f9fafb' }}
                                            contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }}
                                        />
                                        <Bar dataKey="total" fill="#10b981" radius={[6, 6, 0, 0]} barSize={30} />
                                    </BarChart>
                                </ResponsiveContainer>
                            </div>
                        </div>
                    </Deferred>

                    {/* Mutation Trends */}
                    <Deferred data="mutationTrends" fallback={<SkeletonChart height="350px" />}>
                        <div
                            style={{ borderRadius: '24px' }}
                            className="lg:col-span-2 bg-white p-6 border border-gray-100 shadow-sm min-h-[350px] overflow-hidden"
                        >
                            <h3 className="text-sm font-black text-gray-950 mb-4 uppercase tracking-tighter italic">Tren Mutasi (6 Bulan)</h3>
                            <div className="w-full" style={{ height: '240px' }}>
                                <ResponsiveContainer width="100%" height="100%">
                                    <AreaChart data={mutationTrends?.labels?.map((label, index) => ({
                                        name: label,
                                        masuk: mutationTrends.masuk[index],
                                        keluar: mutationTrends.keluar[index],
                                    })) || []}>
                                        <defs>
                                            <linearGradient id="colorMasuk" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="5%" stopColor="#10b981" stopOpacity={0.3} />
                                                <stop offset="95%" stopColor="#10b981" stopOpacity={0} />
                                            </linearGradient>
                                            <linearGradient id="colorKeluar" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="5%" stopColor="#ef4444" stopOpacity={0.3} />
                                                <stop offset="95%" stopColor="#ef4444" stopOpacity={0} />
                                            </linearGradient>
                                        </defs>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                                        <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 9, fontWeight: 800, fill: '#6b7280' }} dy={10} />
                                        <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 9, fontWeight: 800, fill: '#6b7280' }} />
                                        <Tooltip
                                            contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }}
                                        />
                                        <Area type="monotone" dataKey="masuk" stroke="#10b981" strokeWidth={2} fillOpacity={1} fill="url(#colorMasuk)" />
                                        <Area type="monotone" dataKey="keluar" stroke="#ef4444" strokeWidth={2} fillOpacity={1} fill="url(#colorKeluar)" />
                                    </AreaChart>
                                </ResponsiveContainer>
                            </div>
                            <div className="flex gap-4 mt-3">
                                <div className="flex items-center gap-1.5">
                                    <div className="w-2.5 h-2.5 rounded-full bg-green-500" />
                                    <span className="text-[9px] font-black text-gray-600 uppercase tracking-widest">Masuk/Lahir</span>
                                </div>
                                <div className="flex items-center gap-1.5">
                                    <div className="w-2.5 h-2.5 rounded-full bg-red-500" />
                                    <span className="text-[9px] font-black text-gray-600 uppercase tracking-widest">Keluar/Mati</span>
                                </div>
                            </div>
                        </div>
                    </Deferred>

                    {/* Recent Mutations */}
                    <Deferred data="recentMutasi" fallback={<SkeletonActivity />}>
                        <div
                            style={{ borderRadius: '24px' }}
                            className="bg-white p-6 border border-gray-100 shadow-sm flex flex-col h-full min-h-[350px] overflow-hidden"
                        >
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-sm font-black text-gray-950 uppercase tracking-tighter italic">Aktivitas Terbaru</h3>
                                <ArrowUpRight className="w-4 h-4 text-gray-400" />
                            </div>
                            <div className="flex-1 space-y-3">
                                {recentMutasi?.length > 0 ? recentMutasi.map((mut) => (
                                    <div key={mut.id} className="flex items-center gap-3 p-2.5 hover:bg-gray-50 rounded-xl transition-all group border border-transparent hover:border-gray-100">
                                        <div className={cn(
                                            "w-9 h-9 rounded-lg flex items-center justify-center text-[10px] font-black shadow-sm shrink-0",
                                            mut.jenis_mutasi.includes('lahir') ? "bg-green-100 text-green-600" :
                                                mut.jenis_mutasi.includes('mati') ? "bg-red-100 text-red-600" : "bg-blue-100 text-blue-600"
                                        )}>
                                            {mut.jenis_mutasi.substring(0, 1).toUpperCase()}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-black text-gray-950 truncate uppercase tracking-tighter">{mut.penduduk?.nama || 'Tanpa Nama'}</p>
                                            <p className="text-[9px] font-bold text-gray-500 uppercase tracking-tight">{mut.jenis_mutasi.replace('_', ' ')}</p>
                                        </div>
                                        <div className="text-right shrink-0">
                                            <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest">
                                                {new Date(mut.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })}
                                            </p>
                                        </div>
                                    </div>
                                )) : (
                                    <div className="flex flex-col items-center justify-center py-10 text-gray-400">
                                        <Activity className="w-10 h-10 mb-3 opacity-10" />
                                        <p className="text-[10px] font-bold uppercase">Belum ada aktivitas</p>
                                    </div>
                                )}
                            </div>
                            <Link href={route('mutasi.data.index')} className="w-full mt-5 py-2.5 border border-dashed border-gray-200 rounded-xl text-[9px] font-black text-gray-400 uppercase tracking-widest hover:border-green-500 hover:text-green-600 transition-all text-center">
                                LIHAT SEMUA RIWAYAT
                            </Link>
                        </div>
                    </Deferred>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}



function QuickActionCard({ title, desc, href, icon, color }) {
    const iconColors = {
        green: "bg-green-600 shadow-green-200 text-white",
        blue: "bg-blue-600 shadow-blue-200 text-white",
        orange: "bg-orange-600 shadow-orange-200 text-white",
        purple: "bg-purple-600 shadow-purple-200 text-white",
    };

    return (
        <Link 
            href={href} 
            style={{ borderRadius: '24px' }}
            className="p-3 sm:p-5 bg-white border border-gray-100 transition-all hover:scale-[1.03] hover:shadow-xl hover:border-gray-200 group active:scale-95 flex flex-col justify-between h-full shadow-sm overflow-hidden"
        >
            <div>
                <div className={cn("w-8 h-8 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center mb-2 sm:mb-3 shadow-lg transition-transform group-hover:-translate-y-1 group-hover:rotate-6 duration-500", iconColors[color])}>
                    {React.cloneElement(icon, { className: "w-4 h-4 sm:w-6 sm:h-6" })}
                </div>
                <h4 className="font-black text-gray-950 uppercase tracking-tighter text-[10px] sm:text-sm leading-tight italic">{title}</h4>
                <p className="hidden md:block text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500">{desc}</p>
            </div>
            <div className="mt-2 sm:mt-3 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-500">
                <ArrowUpRight className="w-3 h-3 sm:w-4 sm:h-4 text-gray-300" />
            </div>
        </Link>
    );
}

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
    Clock,
    Building2,
    Store,
    HeartHandshake,
    Briefcase,
    MessageSquare,
    Coins,
    CheckCircle2
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

export default function Dashboard({ 
    auth, 
    stats, 
    mutasiStats, 
    suratStats, 
    recentMutasi, 
    ageGroups, 
    mutationTrends,
    umkmStats,
    pengaduanStats,
    bansosStats,
    asetStats,
    proyekStats,
    umkmDistribution,
    asetDistribution
}) {
    const [activeTab, setActiveTab] = React.useState('kependudukan');

    const handleRefresh = () => {
        router.post(route('dashboard.refresh'));
    };

    const formatRupiah = (value) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    };

    const tabs = [
        { id: 'kependudukan', label: 'Kependudukan', icon: Users, color: 'green', desc: 'Demografi & Mutasi' },
        { id: 'pelayanan', label: 'Layanan & Pengaduan', icon: FileText, color: 'blue', desc: 'Surat & Aduan Warga' },
        { id: 'ekonomi', label: 'Ekonomi & UMKM', icon: Store, color: 'orange', desc: 'Potensi Usaha Lokal' },
        { id: 'pembangunan', label: 'Pembangunan & Aset', icon: Building2, color: 'purple', desc: 'Aset & Anggaran Proyek' },
    ];

    return (
        <AuthenticatedLayout
            auth={auth}
            title="Dashboard"
        >
            <Head title="Pusat Kendali Desa" />

            <div className="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-10 text-left">
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
                            <span className="px-2 py-0.5 bg-green-500/30 backdrop-blur-md border border-green-400/30 text-green-100 text-[10px] font-black rounded-full uppercase tracking-widest italic">Live Monitor</span>
                            <span className="text-[10px] text-green-100/60 font-bold uppercase tracking-widest flex items-center gap-1">
                                <Clock className="w-3.5 h-3.5" />
                                {new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                            </span>
                        </div>
                    </div>
                </PageHeader>

                {/* Quick Access Section */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <QuickActionCard
                        title="Tambah Penduduk"
                        href={route('penduduk.index')}
                        icon={<Plus />}
                        color="green"
                    />
                    <QuickActionCard
                        title="Layanan Surat"
                        href={route('admin.surat-pengajuan.index')}
                        icon={<FileText />}
                        color="blue"
                    />
                    <QuickActionCard
                        title="Mutasi Warga"
                        href={route('mutasi.data.index')}
                        icon={<Repeat />}
                        color="orange"
                    />
                    <QuickActionCard
                        title="Manajemen KK"
                        href={route('kk.index')}
                        icon={<Search />}
                        color="purple"
                    />
                </div>

                {/* Actionable Alerts (Global Pending Warning) */}
                <Deferred data="suratStats" fallback={<div className="h-16 w-full bg-gray-50 rounded-[24px] animate-pulse mb-6"></div>}>
                    <div className="space-y-3">
                        {suratStats?.pending > 0 && (
                            <Link
                                href={route('admin.surat-pengajuan.index')}
                                style={{ borderRadius: '16px' }}
                                className="flex items-center justify-between p-4 md:p-5 bg-blue-50 border border-blue-100 group hover:bg-blue-100 transition-all shadow-sm overflow-hidden"
                            >
                                <div className="flex items-center gap-4">
                                    <div className="p-3 bg-blue-600 rounded-xl text-white shadow-md">
                                        <FileText className="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h4 className="text-xs md:text-sm font-black text-blue-900 tracking-tight uppercase">{suratStats.pending} SURAT PENDING</h4>
                                        <p className="text-[10px] md:text-xs text-blue-700 font-semibold uppercase tracking-tight opacity-75">Warga menunggu penyelesaian berkas admin.</p>
                                    </div>
                                </div>
                                <ChevronRight className="w-4 h-4 text-blue-600 group-hover:translate-x-1.5 transition-all" />
                            </Link>
                        )}
                    </div>
                </Deferred>

                {/* Dashboard Tabs Selector */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    {tabs.map((tab) => {
                        const Icon = tab.icon;
                        const isActive = activeTab === tab.id;
                        
                        const tabColors = {
                            green: isActive 
                                ? 'bg-green-600 text-white shadow-lg shadow-green-200 border-green-600' 
                                : 'hover:bg-green-50 text-gray-700 hover:text-green-600 border-gray-100 hover:border-green-200',
                            blue: isActive 
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 border-blue-600' 
                                : 'hover:bg-blue-50 text-gray-700 hover:text-blue-600 border-gray-100 hover:border-blue-200',
                            orange: isActive 
                                ? 'bg-orange-600 text-white shadow-lg shadow-orange-200 border-orange-600' 
                                : 'hover:bg-orange-50 text-gray-700 hover:text-orange-600 border-gray-100 hover:border-orange-200',
                            purple: isActive 
                                ? 'bg-purple-600 text-white shadow-lg shadow-purple-200 border-purple-600' 
                                : 'hover:bg-purple-50 text-gray-700 hover:text-purple-600 border-gray-100 hover:border-purple-200',
                        };

                        return (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                style={{ borderRadius: '16px' }}
                                className={cn(
                                    "p-4 border flex flex-col items-start text-left transition-all duration-300 transform active:scale-95 bg-white shadow-sm",
                                    tabColors[tab.color]
                                )}
                            >
                                <div className={cn(
                                    "w-9 h-9 rounded-lg flex items-center justify-center mb-3 transition-transform duration-300",
                                    isActive ? "bg-white/20 text-white" : "bg-gray-50 text-gray-500"
                                )}>
                                    <Icon className="w-4.5 h-4.5" />
                                </div>
                                <span className="text-xs sm:text-sm font-black uppercase tracking-wider leading-tight italic">{tab.label}</span>
                                <span className={cn(
                                    "text-[10px] sm:text-xs font-semibold tracking-normal mt-1 leading-none",
                                    isActive ? "text-white/80" : "text-gray-400"
                                )}>{tab.desc}</span>
                            </button>
                        );
                    })}
                </div>

                {/* Tab content panels */}
                <div className="relative">
                    {/* Tab 1: Kependudukan */}
                    {activeTab === 'kependudukan' && (
                        <div className="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            {/* Stats Overview */}
                            <Deferred data={["stats"]} fallback={<SkeletonStats />}>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <StatCard
                                        label="Total Penduduk"
                                        value={stats?.total_penduduk}
                                        icon={Users}
                                        badge="Live"
                                        color="green"
                                        sub="Jiwa terdaftar"
                                    />
                                    <StatCard
                                        label="Total KK"
                                        value={stats?.total_kk}
                                        icon={UserCheck}
                                        badge="Aktif"
                                        color="blue"
                                        sub="Keluarga terdata"
                                    />
                                    <StatCard
                                        label="Laki-Laki"
                                        value={stats?.laki_laki}
                                        icon={Users}
                                        badge="Jiwa"
                                        color="teal"
                                        sub="Jiwa laki-laki"
                                    />
                                    <StatCard
                                        label="Perempuan"
                                        value={stats?.perempuan}
                                        icon={Users}
                                        badge="Jiwa"
                                        color="rose"
                                        sub="Jiwa perempuan"
                                    />
                                </div>
                            </Deferred>

                            {/* Charts Section */}
                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                                {/* Gender Distribution */}
                                <Deferred data="stats" fallback={<SkeletonChart height="300px" />}>
                                    <div
                                        style={{ borderRadius: '20px' }}
                                        className="bg-white p-5 border border-gray-100 shadow-sm flex flex-col items-center min-h-[300px] overflow-hidden"
                                    >
                                        <h3 className="text-xs font-black text-gray-950 mb-3 self-start uppercase tracking-tighter italic">Komposisi Gender</h3>
                                        <div className="flex-1 w-full flex items-center justify-center" style={{ minHeight: '160px' }}>
                                            <ResponsiveContainer width="100%" height="100%">
                                                <PieChart>
                                                    <Pie
                                                        data={[
                                                            { name: 'Laki-laki', value: stats?.laki_laki || 0 },
                                                            { name: 'Perempuan', value: stats?.perempuan || 0 },
                                                        ]}
                                                        cx="50%"
                                                        cy="50%"
                                                        innerRadius={45}
                                                        outerRadius={60}
                                                        paddingAngle={4}
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
                                        <div className="flex flex-col gap-1 mt-2 w-full">
                                            {[
                                                { name: 'Laki-laki', value: stats?.laki_laki || 0 },
                                                { name: 'Perempuan', value: stats?.perempuan || 0 },
                                            ].map((item, i) => (
                                                <div key={item.name} className="flex items-center justify-between p-1.5 rounded-lg bg-gray-50 border border-gray-100/50">
                                                    <div className="flex items-center gap-1.5">
                                                        <div className="w-2 h-2 rounded-full" style={{ backgroundColor: COLORS[i] }} />
                                                        <span className="text-[9px] font-black text-gray-700 uppercase tracking-tight">{item.name}</span>
                                                    </div>
                                                    <span className="text-xs font-black text-gray-950">{item.value?.toLocaleString('id-ID')}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </Deferred>

                                {/* Age Groups Chart */}
                                <Deferred data="ageGroups" fallback={<SkeletonChart height="300px" />}>
                                    <div
                                        style={{ borderRadius: '20px' }}
                                        className="lg:col-span-2 bg-white p-5 border border-gray-100 shadow-sm min-h-[300px] overflow-hidden"
                                    >
                                        <h3 className="text-xs font-black text-gray-950 mb-3 uppercase tracking-tighter italic">Demografi Usia</h3>
                                        <div className="w-full flex items-center justify-center" style={{ height: '220px' }}>
                                            <ResponsiveContainer width="100%" height="100%">
                                                <BarChart data={[
                                                    { name: 'Balita (0-5)', total: ageGroups?.balita || 0 },
                                                    { name: 'Anak (6-12)', total: ageGroups?.anak || 0 },
                                                    { name: 'Remaja (13-17)', total: ageGroups?.remaja || 0 },
                                                    { name: 'Dewasa (18-59)', total: ageGroups?.dewasa || 0 },
                                                    { name: 'Lansia (60+)', total: ageGroups?.lansia || 0 },
                                                ]}>
                                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                                                    <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 8, fontWeight: 800, fill: '#6b7280' }} dy={10} />
                                                    <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 8, fontWeight: 800, fill: '#6b7280' }} />
                                                    <Tooltip
                                                        cursor={{ fill: '#f9fafb' }}
                                                        contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }}
                                                    />
                                                    <Bar dataKey="total" fill="#10b981" radius={[4, 4, 0, 0]} barSize={24} />
                                                </BarChart>
                                            </ResponsiveContainer>
                                        </div>
                                    </div>
                                </Deferred>
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                {/* Mutation Trends */}
                                <Deferred data="mutationTrends" fallback={<SkeletonChart height="300px" />}>
                                    <div
                                        style={{ borderRadius: '20px' }}
                                        className="lg:col-span-2 bg-white p-5 border border-gray-100 shadow-sm min-h-[300px] overflow-hidden"
                                    >
                                        <h3 className="text-xs font-black text-gray-950 mb-3 uppercase tracking-tighter italic">Tren Mutasi (6 Bulan)</h3>
                                        <div className="w-full flex items-center justify-center" style={{ height: '210px' }}>
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
                                                    <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 8, fontWeight: 800, fill: '#6b7280' }} dy={10} />
                                                    <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 8, fontWeight: 800, fill: '#6b7280' }} />
                                                    <Tooltip
                                                        contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }}
                                                    />
                                                    <Area type="monotone" dataKey="masuk" stroke="#10b981" strokeWidth={2} fillOpacity={1} fill="url(#colorMasuk)" />
                                                    <Area type="monotone" dataKey="keluar" stroke="#ef4444" strokeWidth={2} fillOpacity={1} fill="url(#colorKeluar)" />
                                                </AreaChart>
                                            </ResponsiveContainer>
                                        </div>
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex items-center gap-1.5">
                                                <div className="w-2.5 h-2.5 rounded-full bg-green-500" />
                                                <span className="text-[8px] font-black text-gray-600 uppercase tracking-widest">Masuk/Lahir</span>
                                            </div>
                                            <div className="flex items-center gap-1.5">
                                                <div className="w-2.5 h-2.5 rounded-full bg-red-500" />
                                                <span className="text-[8px] font-black text-gray-600 uppercase tracking-widest">Keluar/Mati</span>
                                            </div>
                                        </div>
                                    </div>
                                </Deferred>

                                {/* Recent Mutations */}
                                <Deferred data="recentMutasi" fallback={<SkeletonActivity />}>
                                    <div
                                        style={{ borderRadius: '20px' }}
                                        className="bg-white p-5 border border-gray-100 shadow-sm flex flex-col h-full min-h-[300px] overflow-hidden"
                                    >
                                        <div className="flex items-center justify-between mb-3">
                                            <h3 className="text-xs font-black text-gray-950 uppercase tracking-tighter italic">Aktivitas Terbaru</h3>
                                            <ArrowUpRight className="w-3.5 h-3.5 text-gray-400" />
                                        </div>
                                        <div className="flex-1 space-y-2.5">
                                            {recentMutasi?.length > 0 ? recentMutasi.map((mut) => (
                                                <div key={mut.id} className="flex items-center gap-2.5 p-2 hover:bg-gray-50 rounded-xl transition-all group border border-transparent hover:border-gray-100">
                                                    <div className={cn(
                                                        "w-8.5 h-8.5 rounded-lg flex items-center justify-center text-[10px] font-black shadow-sm shrink-0",
                                                        mut.jenis_mutasi.includes('lahir') ? "bg-green-100 text-green-600" :
                                                            mut.jenis_mutasi.includes('mati') ? "bg-red-100 text-red-600" : "bg-blue-100 text-blue-600"
                                                    )}>
                                                        {mut.jenis_mutasi.substring(0, 1).toUpperCase()}
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-xs font-black text-gray-950 truncate uppercase tracking-tighter">{mut.penduduk?.nama || 'Tanpa Nama'}</p>
                                                        <p className="text-[8px] font-bold text-gray-500 uppercase tracking-tight">{mut.jenis_mutasi.replace('_', ' ')}</p>
                                                    </div>
                                                    <div className="text-right shrink-0">
                                                        <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest">
                                                            {new Date(mut.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })}
                                                        </p>
                                                    </div>
                                                </div>
                                            )) : (
                                                <div className="flex flex-col items-center justify-center py-8 text-gray-400">
                                                    <Activity className="w-8 h-8 mb-2 opacity-10" />
                                                    <p className="text-[9px] font-bold uppercase">Belum ada aktivitas</p>
                                                </div>
                                            )}
                                        </div>
                                        <Link href={route('mutasi.data.index')} className="w-full mt-4 py-2 border border-dashed border-gray-200 rounded-xl text-[8px] font-black text-gray-400 uppercase tracking-widest hover:border-green-500 hover:text-green-600 transition-all text-center">
                                            LIHAT RIWAYAT
                                        </Link>
                                    </div>
                                </Deferred>
                            </div>
                        </div>
                    )}

                    {/* Tab 2: Pelayanan & Pengaduan */}
                    {activeTab === 'pelayanan' && (
                        <div className="space-y-4 md:space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            {/* Stats Overview */}
                            <Deferred data={["suratStats", "pengaduanStats"]} fallback={<SkeletonStats />}>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <StatCard
                                        label="Surat Pending"
                                        value={suratStats?.pending}
                                        icon={FileText}
                                        badge="Proses"
                                        color="rose"
                                        sub="Menunggu respon"
                                    />
                                    <StatCard
                                        label="Surat Selesai"
                                        value={suratStats?.selesai}
                                        icon={CheckCircle2}
                                        badge="Selesai"
                                        color="green"
                                        sub="Berhasil dicetak"
                                    />
                                    <StatCard
                                        label="Pengaduan Baru"
                                        value={pengaduanStats?.baru}
                                        icon={AlertTriangle}
                                        badge="Aduan"
                                        color="orange"
                                        sub="Belum ditanggapi"
                                    />
                                    <StatCard
                                        label="Aduan Diproses"
                                        value={pengaduanStats?.diproses}
                                        icon={MessageSquare}
                                        badge="Proses"
                                        color="blue"
                                        sub="Sedang ditangani"
                                    />
                                </div>
                            </Deferred>

                            {/* Complaint Charts & Resources */}
                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                                {/* Complaint Status Composition */}
                                <Deferred data="pengaduanStats" fallback={<SkeletonChart height="300px" />}>
                                    <div
                                        style={{ borderRadius: '20px' }}
                                        className="bg-white p-5 border border-gray-100 shadow-sm flex flex-col items-center min-h-[300px] overflow-hidden"
                                    >
                                        <h3 className="text-xs font-black text-gray-950 mb-3 self-start uppercase tracking-tighter italic">Status Pengaduan Warga</h3>
                                        <div className="flex-1 w-full flex items-center justify-center" style={{ minHeight: '165px' }}>
                                            {pengaduanStats?.total > 0 ? (
                                                <ResponsiveContainer width="100%" height="100%">
                                                    <PieChart>
                                                        <Pie
                                                            data={[
                                                                { name: 'Baru', value: pengaduanStats.baru || 0, color: '#ef4444' },
                                                                { name: 'Diproses', value: pengaduanStats.diproses || 0, color: '#3b82f6' },
                                                                { name: 'Selesai', value: pengaduanStats.selesai || 0, color: '#10b981' },
                                                                { name: 'Ditolak', value: pengaduanStats.ditolak || 0, color: '#6b7280' },
                                                            ].filter(item => item.value > 0)}
                                                            cx="50%"
                                                            cy="50%"
                                                            innerRadius={45}
                                                            outerRadius={60}
                                                            paddingAngle={4}
                                                            dataKey="value"
                                                        >
                                                            {[
                                                                { name: 'Baru', color: '#ef4444' },
                                                                { name: 'Diproses', color: '#3b82f6' },
                                                                { name: 'Selesai', color: '#10b981' },
                                                                { name: 'Ditolak', color: '#6b7280' },
                                                            ].filter(item => (pengaduanStats[item.name.toLowerCase()] || 0) > 0).map((entry, index) => (
                                                                <Cell key={`cell-${index}`} fill={entry.color} />
                                                            ))}
                                                        </Pie>
                                                        <Tooltip
                                                            contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }}
                                                        />
                                                    </PieChart>
                                                </ResponsiveContainer>
                                            ) : (
                                                <div className="text-center py-8 text-gray-400">
                                                    <MessageSquare className="w-10 h-10 mx-auto mb-2 opacity-20" />
                                                    <p className="text-[9px] font-bold uppercase">Belum ada aduan masuk</p>
                                                </div>
                                            )}
                                        </div>
                                        {pengaduanStats?.total > 0 && (
                                            <div className="grid grid-cols-2 gap-1.5 mt-2 w-full">
                                                {[
                                                    { name: 'Baru', value: pengaduanStats.baru || 0, color: 'bg-red-500' },
                                                    { name: 'Diproses', value: pengaduanStats.diproses || 0, color: 'bg-blue-500' },
                                                    { name: 'Selesai', value: pengaduanStats.selesai || 0, color: 'bg-green-500' },
                                                    { name: 'Ditolak', value: pengaduanStats.ditolak || 0, color: 'bg-gray-500' },
                                                ].map((item) => (
                                                    <div key={item.name} className="flex items-center justify-between p-1.5 rounded-lg bg-gray-50 border border-gray-100/50">
                                                        <div className="flex items-center gap-1.5 min-w-0">
                                                            <div className={cn("w-1.5 h-1.5 rounded-full shrink-0", item.color)} />
                                                            <span className="text-[8px] font-black text-gray-700 uppercase tracking-tight truncate">{item.name}</span>
                                                        </div>
                                                        <span className="text-xs font-black text-gray-950 shrink-0">{item.value}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                </Deferred>

                                {/* Public Service Help Card */}
                                <div 
                                    style={{ borderRadius: '20px' }} 
                                    className="lg:col-span-2 bg-white p-5 border border-gray-100 shadow-sm flex flex-col justify-between min-h-[300px] overflow-hidden"
                                >
                                    <div>
                                        <h3 className="text-xs font-black text-gray-950 mb-2 uppercase tracking-tighter italic">Pusat Layanan Warga</h3>
                                        <p className="text-xs text-gray-500 leading-relaxed mb-4">
                                            Gunakan menu ini untuk memonitor surat pengajuan dan aduan dari masyarakat secara real-time. Pastikan semua berkas pending segera diselesaikan untuk menjaga kualitas pelayanan publik Desa Cibatu.
                                        </p>
                                        
                                        <div className="space-y-2.5">
                                            <Link href={route('admin.surat-pengajuan.index')} className="flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 transition-colors border border-blue-100/50 rounded-xl group">
                                                <div className="flex items-center gap-2.5">
                                                    <div className="w-8 h-8 bg-blue-600 rounded-lg text-white flex items-center justify-center shadow-md">
                                                        <FileText className="w-4 h-4" />
                                                    </div>
                                                    <span className="text-xs font-black text-blue-950 uppercase tracking-tight">Proses Surat Pengajuan Warga</span>
                                                </div>
                                                <ChevronRight className="w-3.5 h-3.5 text-blue-600 group-hover:translate-x-1 transition-transform" />
                                            </Link>

                                            <Link href={route('pengaduan.index')} className="flex items-center justify-between p-3 bg-orange-50 hover:bg-orange-100 transition-colors border border-orange-100/50 rounded-xl group">
                                                <div className="flex items-center gap-2.5">
                                                    <div className="w-8 h-8 bg-orange-600 rounded-lg text-white flex items-center justify-center shadow-md">
                                                        <MessageSquare className="w-4 h-4" />
                                                    </div>
                                                    <span className="text-xs font-black text-orange-950 uppercase tracking-tight">Kelola Pengaduan Masyarakat</span>
                                                </div>
                                                <ChevronRight className="w-3.5 h-3.5 text-orange-600 group-hover:translate-x-1 transition-transform" />
                                            </Link>
                                        </div>
                                    </div>

                                    <div className="pt-3 border-t border-gray-100 flex items-center justify-between text-gray-400">
                                        <span className="text-[9px] font-bold uppercase tracking-wider">Total Aduan Masuk:</span>
                                        <span className="text-xs font-black text-gray-900">{pengaduanStats?.total || 0} Laporan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Tab 3: Ekonomi & UMKM */}
                    {activeTab === 'ekonomi' && (
                        <div className="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            {/* Stats Overview */}
                            <Deferred data={["umkmStats"]} fallback={<SkeletonStats />}>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <StatCard
                                        label="Total UMKM"
                                        value={umkmStats?.total}
                                        icon={Store}
                                        badge="Terdaftar"
                                        color="orange"
                                        sub="Pelaku usaha terdaftar"
                                    />
                                    <StatCard
                                        label="UMKM Aktif"
                                        value={umkmStats?.aktif}
                                        icon={CheckCircle2}
                                        badge="Aktif"
                                        color="green"
                                        sub="Usaha beroperasi aktif"
                                    />
                                    <StatCard
                                        label="Terverifikasi"
                                        value={umkmStats?.verified}
                                        icon={UserCheck}
                                        badge="Verified"
                                        color="blue"
                                        sub="Validasi Pemdes"
                                    />
                                    <StatCard
                                        label="Keaktifan"
                                        value={umkmStats?.total > 0 ? Math.round((umkmStats.aktif / umkmStats.total) * 100) + '%' : '0%'}
                                        icon={Activity}
                                        badge="Rasio"
                                        color="purple"
                                        sub="Rasio keaktifan usaha"
                                    />
                                </div>
                            </Deferred>

                            {/* Business Type Distribution Chart */}
                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <Deferred data="umkmDistribution" fallback={<SkeletonChart height="300px" />}>
                                    <div 
                                        style={{ borderRadius: '20px' }} 
                                        className="lg:col-span-2 bg-white p-5 border border-gray-100 shadow-sm min-h-[300px] flex flex-col justify-between overflow-hidden"
                                    >
                                        <div>
                                            <h3 className="text-xs font-black text-gray-950 mb-4 uppercase tracking-tighter italic">Distribusi Jenis Usaha UMKM</h3>
                                            <div className="w-full flex items-center justify-center" style={{ height: '210px' }}>
                                                {umkmDistribution?.length > 0 ? (
                                                    <ResponsiveContainer width="100%" height="100%">
                                                        <BarChart
                                                            data={umkmDistribution}
                                                            layout="vertical"
                                                            margin={{ top: 0, right: 30, left: 10, bottom: 5 }}
                                                        >
                                                            <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#f3f4f6" />
                                                            <XAxis type="number" axisLine={false} tickLine={false} tick={{ fontSize: 8, fontWeight: 800, fill: '#6b7280' }} />
                                                            <YAxis dataKey="name" type="category" axisLine={false} tickLine={false} tick={{ fontSize: 8, fontWeight: 800, fill: '#6b7280' }} width={90} />
                                                            <Tooltip contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '10px' }} />
                                                            <Bar dataKey="value" fill="#f59e0b" radius={[0, 4, 4, 0]} barSize={12} />
                                                        </BarChart>
                                                    </ResponsiveContainer>
                                                ) : (
                                                    <div className="flex flex-col items-center justify-center h-full text-gray-400">
                                                        <Store className="w-10 h-10 mb-2 opacity-15" />
                                                        <p className="text-[9px] font-bold uppercase">Belum ada data distribusi</p>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </Deferred>

                                <div 
                                    style={{ borderRadius: '20px' }} 
                                    className="bg-white p-5 border border-gray-100 shadow-sm flex flex-col justify-between min-h-[300px] overflow-hidden"
                                >
                                    <div>
                                        <h3 className="text-xs font-black text-gray-950 mb-2 uppercase tracking-tighter italic">Ekonomi Desa Mandiri</h3>
                                        <p className="text-xs text-gray-500 leading-relaxed mb-4">
                                            Pengembangan UMKM adalah tulang punggung perekonomian mandiri Desa Cibatu. Kelola profil usaha, verifikasi pendaftaran baru, dan pantau penyebaran usaha untuk memetakan potensi lokal desa.
                                        </p>
                                        
                                        <Link 
                                            href={route('umkm.index')} 
                                            className="w-full py-3 bg-orange-600 hover:bg-orange-700 transition-colors text-white font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 rounded-xl shadow-lg shadow-orange-100"
                                        >
                                            <Store className="w-4 h-4" />
                                            KELOLA DATA UMKM
                                        </Link>
                                    </div>

                                    <div className="pt-3 border-t border-gray-100 flex items-center justify-between text-gray-400">
                                        <span className="text-[9px] font-bold uppercase tracking-wider">Perekonomian Lokal:</span>
                                        <span className="text-xs font-black text-orange-600 uppercase tracking-widest">Cibatu Mandiri</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Tab 4: Pembangunan & Aset */}
                    {activeTab === 'pembangunan' && (
                        <div className="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            {/* Stats Overview */}
                            <Deferred data={["asetStats", "proyekStats", "bansosStats"]} fallback={<SkeletonStats />}>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <StatCard
                                        label="Inventaris Aset"
                                        value={asetStats?.total_inventaris}
                                        icon={Building2}
                                        badge="Item Aset"
                                        color="purple"
                                        sub="Barang prasarana desa"
                                    />
                                    <StatCard
                                        label="Total Nilai Buku"
                                        value={formatRupiah(asetStats?.total_nilai || 0)}
                                        icon={Coins}
                                        badge="Rupiah"
                                        color="green"
                                        sub="Kapitalisasi nilai aset"
                                    />
                                    <StatCard
                                        label="Proyek Pembangunan"
                                        value={proyekStats?.total}
                                        icon={Briefcase}
                                        badge="Proyek"
                                        color="blue"
                                        sub={`Pelaksanaan: ${proyekStats?.pelaksanaan || 0}`}
                                    />
                                    <StatCard
                                        label="Program Bansos"
                                        value={bansosStats?.total_program}
                                        icon={HeartHandshake}
                                        badge="Bansos"
                                        color="rose"
                                        sub={`Penerima: ${bansosStats?.total_penerima || 0}`}
                                    />
                                </div>
                            </Deferred>

                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                {/* Project Realization */}
                                <Deferred data="proyekStats" fallback={<SkeletonChart height="300px" />}>
                                    <div 
                                        style={{ borderRadius: '20px' }} 
                                        className="bg-white p-5 border border-gray-100 shadow-sm flex flex-col justify-between min-h-[300px] overflow-hidden"
                                    >
                                        <div>
                                            <h3 className="text-xs font-black text-gray-950 mb-4 uppercase tracking-tighter italic">Realisasi Anggaran Proyek</h3>
                                            
                                            <div className="space-y-4">
                                                <div>
                                                    <div className="flex justify-between items-baseline mb-1">
                                                        <span className="text-[9px] font-black text-gray-400 uppercase tracking-wider">Total Anggaran</span>
                                                        <span className="text-xs font-black text-gray-950">{formatRupiah(proyekStats?.anggaran || 0)}</span>
                                                    </div>
                                                    <div className="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                        <div className="bg-blue-600 h-full rounded-full" style={{ width: '100%' }} />
                                                    </div>
                                                </div>

                                                <div>
                                                    <div className="flex justify-between items-baseline mb-1">
                                                        <span className="text-[9px] font-black text-gray-400 uppercase tracking-wider">Realisasi Belanja</span>
                                                        <span className="text-xs font-black text-green-600">{formatRupiah(proyekStats?.realisasi || 0)}</span>
                                                    </div>
                                                    <div className="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                        <div 
                                                            className="bg-green-500 h-full rounded-full transition-all duration-1000" 
                                                            style={{ width: `${proyekStats?.anggaran > 0 ? Math.min(Math.round((proyekStats.realisasi / proyekStats.anggaran) * 100), 100) : 0}%` }} 
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="mt-6 p-3 bg-gray-50 border border-gray-100/50 rounded-xl text-center">
                                                <span className="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Tingkat Penyerapan Anggaran</span>
                                                <span className="text-xl font-black text-gray-950 italic">
                                                    {proyekStats?.anggaran > 0 ? Math.round((proyekStats.realisasi / proyekStats.anggaran) * 100) : 0}%
                                                </span>
                                            </div>
                                        </div>

                                        <Link href={route('transparansi-desa.index')} className="w-full py-2.5 border border-dashed border-gray-200 rounded-xl text-center text-[9px] font-black text-gray-400 uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition-colors">
                                            MONITOR KEUANGAN
                                        </Link>
                                    </div>
                                </Deferred>

                                {/* Asset Values per Category */}
                                <Deferred data="asetDistribution" fallback={<SkeletonChart height="300px" />}>
                                    <div 
                                        style={{ borderRadius: '20px' }} 
                                        className="lg:col-span-2 bg-white p-5 border border-gray-100 shadow-sm min-h-[300px] flex flex-col justify-between overflow-hidden"
                                    >
                                        <div>
                                            <h3 className="text-xs font-black text-gray-950 mb-4 uppercase tracking-tighter italic">Nilai Aset per Kategori</h3>
                                            <div className="w-full flex items-center justify-center" style={{ height: '210px' }}>
                                                {asetDistribution?.length > 0 ? (
                                                    <ResponsiveContainer width="100%" height="100%">
                                                        <BarChart
                                                            data={asetDistribution}
                                                            layout="vertical"
                                                            margin={{ top: 0, right: 30, left: 10, bottom: 5 }}
                                                        >
                                                            <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#f3f4f6" />
                                                            <XAxis type="number" axisLine={false} tickLine={false} tick={{ fontSize: 7, fontWeight: 800, fill: '#6b7280' }} />
                                                            <YAxis dataKey="name" type="category" axisLine={false} tickLine={false} tick={{ fontSize: 7, fontWeight: 800, fill: '#6b7280' }} width={125} />
                                                            <Tooltip 
                                                                formatter={(value) => [formatRupiah(value), 'Nilai Buku']}
                                                                contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)', fontSize: '9px' }} 
                                                            />
                                                            <Bar dataKey="value" fill="#8b5cf6" radius={[0, 4, 4, 0]} barSize={12} />
                                                        </BarChart>
                                                    </ResponsiveContainer>
                                                ) : (
                                                    <div className="flex flex-col items-center justify-center h-full text-gray-400">
                                                        <Building2 className="w-10 h-10 mb-2 opacity-15" />
                                                        <p className="text-[9px] font-bold uppercase">Belum ada data distribusi nilai aset</p>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </Deferred>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function QuickActionCard({ title, href, icon, color }) {
    const iconColors = {
        green: "bg-green-50 text-green-600 border-green-100",
        blue: "bg-blue-50 text-blue-600 border-blue-100",
        orange: "bg-orange-50 text-orange-600 border-orange-100",
        purple: "bg-purple-50 text-purple-600 border-purple-100",
    };

    return (
        <Link 
            href={href} 
            style={{ borderRadius: '14px' }}
            className="p-3.5 bg-white border border-gray-100/80 transition-all hover:scale-[1.02] hover:shadow-md hover:border-gray-200 active:scale-95 flex items-center justify-between shadow-sm group"
        >
            <div className="flex items-center gap-3 min-w-0">
                <div className={cn("w-9 h-9 rounded-xl flex items-center justify-center shrink-0 border transition-transform group-hover:scale-105 duration-350", iconColors[color])}>
                    {React.cloneElement(icon, { className: "w-4.5 h-4.5" })}
                </div>
                <h4 className="font-black text-gray-950 uppercase tracking-tight text-xs sm:text-sm leading-none italic truncate">{title}</h4>
            </div>
            <ArrowUpRight className="w-3.5 h-3.5 text-gray-300 group-hover:text-gray-500 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" />
        </Link>
    );
}

import React from 'react';
import { Head, Link } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, StatCard, Badge } from '@/Components/Shared';
import { 
    Building2, 
    CheckCircle2, 
    Users, 
    HardDrive, 
    Shield, 
    Clock, 
    ArrowRight, 
    TrendingUp, 
    Database, 
    Activity, 
    Calendar,
    ArrowUpRight
} from 'lucide-react';
import { 
    ResponsiveContainer, 
    AreaChart, 
    Area, 
    XAxis, 
    YAxis, 
    CartesianGrid, 
    Tooltip, 
    BarChart, 
    Bar, 
    Legend 
} from 'recharts';

// Custom Tooltip component for Recharts with dark premium design
const CustomTooltip = ({ active, payload, label }) => {
    if (active && payload && payload.length) {
        return (
            <div className="bg-slate-900 border border-slate-800 text-white p-4 rounded-2xl shadow-2xl text-xs space-y-1.5 backdrop-blur-md bg-opacity-95">
                <p className="text-slate-400 font-extrabold uppercase tracking-widest text-[10px] mb-1">{label}</p>
                {payload.map((item, idx) => (
                    <div key={idx} className="flex items-center justify-between gap-4">
                        <span className="flex items-center gap-1.5 text-slate-300 font-medium">
                            <span className="w-2 h-2 rounded-full" style={{ backgroundColor: item.color || item.fill }}></span>
                            {item.name}:
                        </span>
                        <span className="font-mono font-black text-white">{item.value}</span>
                    </div>
                ))}
            </div>
        );
    }
    return null;
};

export default function Index({ stats, charts, recentTenants, recentLogs }) {
    const registrationData = charts?.registration_trend || [];
    const storageData = charts?.storage_distribution || [];
    const userData = charts?.user_distribution || [];

    return (
        <LandlordLayout>
            <Head title="Landlord Dashboard" />

            <div className="space-y-8 pb-12">
                {/* Header */}
                <PageHeader 
                    icon={Shield}
                    title="Landlord Dashboard"
                    subtitle="Selamat Datang di Panel Manajemen Pusat SaaS Sistem Desa Terpadu."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {/* Stats Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatCard 
                        icon={Building2}
                        label="Total Desa"
                        value={stats.total_tenants}
                        color="blue"
                        badge="Tenant"
                    />
                    <StatCard 
                        icon={CheckCircle2}
                        label="Desa Aktif"
                        value={stats.active_tenants}
                        color="green"
                        badge="Status"
                    />
                    <StatCard 
                        icon={Users}
                        label="Total Kuota User"
                        value={stats.total_users_limit}
                        color="purple"
                        badge="Limit"
                    />
                    <StatCard 
                        icon={HardDrive}
                        label="Total Storage"
                        value={`${(stats.total_storage_limit / 1024).toFixed(1)} GB`}
                        color="yellow"
                        badge="Disk"
                    />
                </div>

                {/* Charts Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* Growth Chart */}
                    <div className="lg:col-span-8 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm space-y-6">
                        <div className="flex items-center justify-between">
                            <div className="space-y-1">
                                <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                    <TrendingUp className="w-5 h-5 text-indigo-500" />
                                    Tren Pendaftaran Desa
                                </h3>
                                <p className="text-xs text-slate-500 font-medium">Statistik registrasi desa baru selama 6 bulan terakhir.</p>
                            </div>
                            <span className="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-wider rounded-lg">6 Bulan Terakhir</span>
                        </div>
                        <div className="h-72 w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={registrationData} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                                    <defs>
                                        <linearGradient id="colorReg" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#4f46e5" stopOpacity={0.2}/>
                                            <stop offset="95%" stopColor="#4f46e5" stopOpacity={0}/>
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                    <XAxis dataKey="month" stroke="#94a3b8" fontSize={10} fontWeight="bold" tickLine={false} axisLine={false} />
                                    <YAxis stroke="#94a3b8" fontSize={10} fontWeight="bold" tickLine={false} axisLine={false} allowDecimals={false} />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Area type="monotone" name="Desa Baru" dataKey="count" stroke="#4f46e5" strokeWidth={3} fillOpacity={1} fill="url(#colorReg)" />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Storage Limits Chart */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm space-y-6">
                        <div className="space-y-1">
                            <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                <Database className="w-5 h-5 text-amber-500" />
                                Alokasi Storage Terbesar
                            </h3>
                            <p className="text-xs text-slate-500 font-medium">Top 5 desa dengan penggunaan disk terbesar (MB).</p>
                        </div>
                        <div className="h-72 w-full">
                            {storageData.length === 0 ? (
                                <div className="h-full flex items-center justify-center text-slate-400 font-bold text-xs uppercase">Belum ada data desa</div>
                            ) : (
                                <ResponsiveContainer width="100%" height="100%">
                                    <BarChart data={storageData} margin={{ top: 10, right: 0, left: -20, bottom: 0 }} barGap={2}>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                        <XAxis dataKey="name" stroke="#94a3b8" fontSize={10} fontWeight="bold" tickLine={false} axisLine={false} />
                                        <YAxis stroke="#94a3b8" fontSize={10} fontWeight="bold" tickLine={false} axisLine={false} />
                                        <Tooltip content={<CustomTooltip />} />
                                        <Legend verticalAlign="top" height={36} iconType="circle" iconSize={8} wrapperStyle={{ fontSize: 10, fontWeight: 'bold' }} />
                                        <Bar name="Terpakai (MB)" dataKey="used" fill="#f59e0b" radius={[4, 4, 0, 0]} />
                                        <Bar name="Kuota (MB)" dataKey="limit" fill="#e2e8f0" radius={[4, 4, 0, 0]} />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </div>
                    </div>
                </div>

                {/* Second Grid: User Chart, Recent Tenants, Recent Logs */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* User Quota Distribution Chart */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm space-y-6">
                        <div className="space-y-1">
                            <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                <Users className="w-5 h-5 text-purple-500" />
                                Penggunaan Kuota User
                            </h3>
                            <p className="text-xs text-slate-500 font-medium">Top 5 desa dengan jumlah admin & perangkat terdaftar.</p>
                        </div>
                        <div className="h-72 w-full">
                            {userData.length === 0 ? (
                                <div className="h-full flex items-center justify-center text-slate-400 font-bold text-xs uppercase">Belum ada data desa</div>
                            ) : (
                                <ResponsiveContainer width="100%" height="100%">
                                    <BarChart data={userData} margin={{ top: 10, right: 0, left: -20, bottom: 0 }} barGap={2}>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                        <XAxis dataKey="name" stroke="#94a3b8" fontSize={10} fontWeight="bold" tickLine={false} axisLine={false} />
                                        <YAxis stroke="#94a3b8" fontSize={10} fontWeight="bold" tickLine={false} axisLine={false} />
                                        <Tooltip content={<CustomTooltip />} />
                                        <Legend verticalAlign="top" height={36} iconType="circle" iconSize={8} wrapperStyle={{ fontSize: 10, fontWeight: 'bold' }} />
                                        <Bar name="User Aktif" dataKey="active_users" fill="#8b5cf6" radius={[4, 4, 0, 0]} />
                                        <Bar name="Kuota" dataKey="max_users" fill="#e2e8f0" radius={[4, 4, 0, 0]} />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </div>
                    </div>

                    {/* Recent Registered Villages */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm flex flex-col justify-between">
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="space-y-1">
                                    <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                        <Calendar className="w-5 h-5 text-emerald-500" />
                                        Desa Baru Onboard
                                    </h3>
                                    <p className="text-xs text-slate-500 font-medium">5 Desa yang paling baru mendaftar di sistem.</p>
                                </div>
                            </div>

                            <div className="divide-y divide-gray-50">
                                {recentTenants.length === 0 ? (
                                    <div className="py-8 text-center text-sm font-bold text-gray-400">Belum ada desa terdaftar.</div>
                                ) : (
                                    recentTenants.map((row) => (
                                        <div key={row.id} className="py-3 flex items-center justify-between gap-4">
                                            <div className="min-w-0">
                                                <p className="font-black text-sm text-slate-800 truncate">{row.name}</p>
                                                <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">ID: {row.id}</p>
                                            </div>
                                            <div className="flex items-center gap-2 shrink-0">
                                                <Badge 
                                                    color={row.is_active ? 'green' : 'red'}
                                                    dot={row.is_active ? 'green' : 'red'}
                                                    className="text-[10px]"
                                                >
                                                    {row.is_active ? 'Aktif' : 'Nonaktif'}
                                                </Badge>
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                        </div>
                        <div className="mt-4 pt-4 border-t border-gray-50 flex">
                            <Link 
                                href={route('tenants.index')}
                                className="w-full text-center py-2 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 text-xs font-black text-slate-600 hover:text-indigo-600 rounded-2xl flex items-center justify-center gap-2 transition-all group"
                            >
                                Kelola Seluruh Desa
                                <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                        </div>
                    </div>

                    {/* Recent Central Logs */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm flex flex-col justify-between">
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="space-y-1">
                                    <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                        <Activity className="w-5 h-5 text-sky-500" />
                                        Log Aktivitas Terkini
                                    </h3>
                                    <p className="text-xs text-slate-500 font-medium">Catatan log aktivitas terdaftar terakhir.</p>
                                </div>
                            </div>

                            <div className="space-y-3">
                                {recentLogs.length === 0 ? (
                                    <div className="py-8 text-center text-sm font-bold text-gray-400">Belum ada catatan aktivitas.</div>
                                ) : (
                                    recentLogs.map((log) => {
                                        const actionColors = {
                                            created: 'blue',
                                            user_created: 'green',
                                            user_deleted: 'red',
                                            file_uploaded: 'purple',
                                        };
                                        return (
                                            <div key={log.id} className="p-3 bg-slate-50 rounded-2xl border border-slate-100/50 flex flex-col gap-1.5">
                                                <div className="flex items-center justify-between">
                                                    <span className="font-extrabold text-[10px] text-slate-500 truncate max-w-[120px]">{log.tenant_name}</span>
                                                    <Badge 
                                                        color={actionColors[log.action] || 'slate'} 
                                                        className="text-[9px] scale-90 origin-right"
                                                    >
                                                        {log.action.replace('_', ' ').toUpperCase()}
                                                    </Badge>
                                                </div>
                                                <p className="text-xs text-slate-700 font-medium line-clamp-1">{log.description}</p>
                                                <span className="text-[9px] text-slate-400 font-bold flex items-center gap-1 mt-0.5">
                                                    <Clock className="w-3 h-3 text-slate-300" />
                                                    {new Date(log.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                                </span>
                                            </div>
                                        );
                                    })
                                )}
                            </div>
                        </div>
                        <div className="mt-4 pt-4 border-t border-gray-50 flex">
                            <Link 
                                href={route('landlord.monitoring.index')}
                                className="w-full text-center py-2 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 text-xs font-black text-slate-600 hover:text-indigo-600 rounded-2xl flex items-center justify-center gap-2 transition-all group"
                            >
                                Lihat Semua Log & Monitoring
                                <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Bottom Central Connection Card */}
                <div className="bg-slate-900 rounded-3xl border border-slate-800 p-8 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 text-white relative overflow-hidden group">
                    {/* Decorative lights */}
                    <div className="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-500/20 transition-all duration-700"></div>
                    
                    <div className="space-y-2 max-w-2xl relative z-10">
                        <h3 className="text-lg font-black uppercase italic tracking-tight text-white flex items-center gap-2">
                            <span className="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            Koneksi Central Aktif
                        </h3>
                        <p className="text-slate-400 text-sm leading-relaxed">
                            Saat ini Anda terhubung langsung dengan basis data utama (<code className="font-mono bg-slate-800 px-1.5 py-0.5 rounded text-indigo-400 text-xs font-black">db_central</code>). 
                            Segala perubahan pada menu <strong>Manajemen Desa</strong> atau <strong>Alokasi Resource</strong> akan berdampak langsung ke seluruh ekosistem tenant desa secara real-time.
                        </p>
                    </div>
                    <div className="px-6 py-3 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl text-xs font-black text-indigo-400 uppercase tracking-widest italic shrink-0 relative z-10">
                        Central Context
                    </div>
                </div>
            </div>
        </LandlordLayout>
    );
}

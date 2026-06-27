import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { StatCard, Badge } from '@/Components/Shared';
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
    ArrowUpRight,
    Server,
    Layers,
    ChevronRight
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
            <div className="bg-slate-950 border border-slate-900 text-white p-4 rounded-2xl shadow-2xl text-xs space-y-1.5 backdrop-blur-md bg-opacity-95">
                <p className="text-slate-400 font-extrabold uppercase tracking-widest text-[9px] mb-1">{label}</p>
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
    
    const [timeString, setTimeString] = useState('');

    useEffect(() => {
        const updateTime = () => {
            const now = new Date();
            setTimeString(now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }));
        };
        updateTime();
    }, []);

    return (
        <LandlordLayout>
            <Head title="Landlord Dashboard" />

            <div className="space-y-8 pb-12 animate-in fade-in duration-500">
                {/* Custom Welcome Hero Banner */}
                <div className="relative overflow-hidden rounded-3xl bg-slate-950 text-white border border-slate-900 shadow-xl p-8 md:p-10 group">
                    <div className="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-500/15 transition-all duration-700"></div>
                    <div className="absolute -bottom-10 -left-10 w-80 h-80 bg-purple-500/5 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div className="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div className="space-y-2">
                            <div className="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/10 border border-indigo-500/25 text-indigo-400 rounded-full text-[10px] font-black uppercase tracking-wider">
                                <Server className="w-3.5 h-3.5 animate-pulse" />
                                Live Central SaaS
                            </div>
                            <h2 className="text-2xl md:text-3xl font-black tracking-tight leading-none text-white italic uppercase">
                                Central Landlord Panel
                            </h2>
                            <p className="text-slate-400 text-xs font-medium max-w-xl">
                                Selamat datang kembali! Di sini Anda dapat memantau utilisasi alokasi storage, tren registrasi desa, dan memodifikasi konfigurasi SaaS Sistem Desa Cibatu.
                            </p>
                        </div>
                        
                        <div className="flex flex-col items-start md:items-end text-xs font-bold text-slate-450 shrink-0 gap-1.5">
                            <div className="flex items-center gap-2">
                                <Calendar className="w-4 h-4 text-indigo-400" />
                                <span className="text-slate-200">{timeString || 'Mengakses Sistem...'}</span>
                            </div>
                            <span className="px-2.5 py-0.5 bg-slate-900 rounded-lg border border-slate-800 text-[10px] uppercase font-bold text-gray-400 tracking-wider">
                                Diskominfo Purwakarta
                            </span>
                        </div>
                    </div>
                </div>

                {/* Stats Grid with Hover Animations */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="hover:-translate-y-1 transition-all duration-300">
                        <StatCard 
                            icon={Building2}
                            label="Total Desa"
                            value={stats.total_tenants}
                            color="blue"
                            badge="Tenant"
                        />
                    </div>
                    <div className="hover:-translate-y-1 transition-all duration-300">
                        <StatCard 
                            icon={CheckCircle2}
                            label="Desa Aktif"
                            value={stats.active_tenants}
                            color="green"
                            badge="Status"
                        />
                    </div>
                    <div className="hover:-translate-y-1 transition-all duration-300">
                        <StatCard 
                            icon={Users}
                            label="Total Kuota User"
                            value={stats.total_users_limit}
                            color="purple"
                            badge="Limit"
                        />
                    </div>
                    <div className="hover:-translate-y-1 transition-all duration-300">
                        <StatCard 
                            icon={HardDrive}
                            label="Total Storage"
                            value={`${(stats.total_storage_limit / 1024).toFixed(1)} GB`}
                            color="yellow"
                            badge="Disk"
                        />
                    </div>
                </div>

                {/* Charts Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* Growth Chart */}
                    <div className="lg:col-span-8 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow space-y-6">
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
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow space-y-6">
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
                                        <Bar name="Terpakai (MB)" dataKey="used" fill="#f59e0b" radius={[6, 6, 0, 0]} />
                                        <Bar name="Kuota (MB)" dataKey="limit" fill="#e2e8f0" radius={[6, 6, 0, 0]} />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </div>
                    </div>
                </div>

                {/* Second Grid: User Chart, Recent Tenants, Recent Logs */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* User Quota Distribution Chart */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow space-y-6">
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
                                        <Bar name="User Aktif" dataKey="active_users" fill="#8b5cf6" radius={[6, 6, 0, 0]} />
                                        <Bar name="Kuota" dataKey="max_users" fill="#e2e8f0" radius={[6, 6, 0, 0]} />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </div>
                    </div>

                    {/* Recent Registered Villages */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                        <div className="space-y-5">
                            <div className="space-y-1">
                                <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                    <Calendar className="w-5 h-5 text-emerald-500" />
                                    Desa Baru Onboard
                                </h3>
                                <p className="text-xs text-slate-500 font-medium">5 Desa yang paling baru mendaftar di sistem.</p>
                            </div>

                            <div className="space-y-2.5">
                                {recentTenants.length === 0 ? (
                                    <div className="py-8 text-center text-sm font-bold text-gray-400">Belum ada desa terdaftar.</div>
                                ) : (
                                    recentTenants.map((row) => {
                                        const initials = row.name ? row.name.replace(/^(desa|kelurahan)\s+/i, '').substring(0, 2).toUpperCase() : 'DS';
                                        return (
                                            <div key={row.id} className="p-3 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded-2xl flex items-center justify-between gap-4 transition-all">
                                                <div className="flex items-center gap-3 min-w-0">
                                                    <div className="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-650 font-black text-xs flex items-center justify-center shrink-0 select-none">
                                                        {initials}
                                                    </div>
                                                    <div className="min-w-0 text-left">
                                                        <p className="font-bold text-sm text-slate-850 truncate">{row.name}</p>
                                                        <span className="inline-flex px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[9px] font-bold font-mono rounded mt-0.5 border border-gray-200">
                                                            {row.id}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-2 shrink-0">
                                                    <Badge 
                                                        color={row.is_active ? 'green' : 'red'}
                                                        dot={row.is_active ? 'green' : 'red'}
                                                        className="text-[9px]"
                                                    >
                                                        {row.is_active ? 'Aktif' : 'Nonaktif'}
                                                    </Badge>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </div>
                        </div>
                        <div className="mt-6 pt-4 border-t border-gray-100 flex">
                            <Link 
                                href={route('tenants.index')}
                                className="w-full text-center py-3 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 text-xs font-black text-slate-650 hover:text-indigo-600 rounded-2xl flex items-center justify-center gap-2 transition-all group cursor-pointer"
                            >
                                Kelola Seluruh Desa
                                <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                        </div>
                    </div>

                    {/* Recent Central Logs - Refactored as Timeline Feed */}
                    <div className="lg:col-span-4 bg-white border border-gray-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                        <div className="space-y-5">
                            <div className="space-y-1">
                                <h3 className="text-base font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                                    <Activity className="w-5 h-5 text-sky-500" />
                                    Log Aktivitas Terkini
                                </h3>
                                <p className="text-xs text-slate-500 font-medium">Catatan log aktivitas central terdaftar terakhir.</p>
                            </div>

                            <div className="relative pl-4 space-y-4 border-l-2 border-slate-100 py-1 text-left">
                                {recentLogs.length === 0 ? (
                                    <div className="py-8 text-center text-sm font-bold text-gray-400 -ml-4">Belum ada catatan aktivitas.</div>
                                ) : (
                                    recentLogs.map((log) => {
                                        const actionColors = {
                                            created: 'bg-blue-500 border-blue-200 text-blue-700',
                                            user_created: 'bg-emerald-500 border-emerald-250 text-emerald-700',
                                            user_deleted: 'bg-rose-500 border-rose-250 text-rose-700',
                                            file_uploaded: 'bg-purple-500 border-purple-250 text-purple-700',
                                        };
                                        const actionDotColor = log.action === 'user_deleted' ? 'bg-rose-500 shadow-rose-500/20' 
                                                             : log.action === 'user_created' ? 'bg-emerald-500 shadow-emerald-500/20'
                                                             : 'bg-indigo-500 shadow-indigo-500/20';

                                        return (
                                            <div key={log.id} className="relative group/item text-left">
                                                {/* Timeline bullet dot */}
                                                <div className={`absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full ${actionDotColor} border-2 border-white shadow-md z-10 transition-transform group-hover/item:scale-125`}></div>
                                                
                                                <div className="space-y-0.5">
                                                    <div className="flex items-center justify-between gap-2">
                                                        <span className="font-extrabold text-[10px] text-slate-700 truncate" title={log.tenant_name}>
                                                            {log.tenant_name}
                                                        </span>
                                                        <span className="text-[9px] font-bold text-slate-400 font-mono flex items-center">
                                                            {new Date(log.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                                        </span>
                                                    </div>
                                                    <p className="text-xs text-slate-500 leading-normal font-medium pr-2">
                                                        {log.description}
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </div>
                        </div>
                        <div className="mt-6 pt-4 border-t border-gray-100 flex">
                            <Link 
                                href={route('landlord.monitoring.index')}
                                className="w-full text-center py-3 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 text-xs font-black text-slate-650 hover:text-indigo-600 rounded-2xl flex items-center justify-center gap-2 transition-all group cursor-pointer"
                            >
                                Lihat Semua Log & Monitoring
                                <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Bottom Central Connection Card */}
                <div className="bg-slate-950 rounded-3xl border border-slate-900 p-8 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 text-white relative overflow-hidden group">
                    {/* Decorative lights */}
                    <div className="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-500/20 transition-all duration-700"></div>
                    
                    <div className="space-y-2 max-w-2xl relative z-10 text-left">
                        <h3 className="text-base font-black uppercase italic tracking-tight text-white flex items-center gap-2 leading-none">
                            <span className="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/30"></span>
                            Koneksi Central Aktif
                        </h3>
                        <p className="text-slate-400 text-xs leading-relaxed font-medium">
                            Saat ini Anda terhubung langsung dengan basis data utama (<code className="font-mono bg-slate-900 px-1.5 py-0.5 rounded text-indigo-400 text-[10px] font-bold border border-slate-800">db_central</code>). 
                            Segala perubahan pada menu <strong>Manajemen Desa</strong> atau <strong>Alokasi Resource</strong> akan berdampak langsung ke seluruh ekosistem tenant desa secara real-time.
                        </p>
                    </div>
                    <div className="px-6 py-3 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl text-[10px] font-black text-indigo-400 uppercase tracking-widest italic shrink-0 relative z-10">
                        Central Context
                    </div>
                </div>
            </div>
        </LandlordLayout>
    );
}

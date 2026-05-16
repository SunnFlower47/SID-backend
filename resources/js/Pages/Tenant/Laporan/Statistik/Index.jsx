import React from 'react';
import { Head, Link, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GenderPieChart from '@/Components/Laporan/GenderPieChart';
import AgeBarChart from '@/Components/Laporan/AgeBarChart';
import MutasiBarChart from '@/Components/Laporan/MutasiBarChart';
import SkeletonChart from '@/Components/Shared/Skeleton/SkeletonChart';
import { 
    BarChart3, Users, Baby, Skull, TrendingUp, ChevronLeft, 
    PieChart, Map, Building2, Briefcase, GraduationCap, Heart,
    RefreshCw
} from 'lucide-react';
import { cn } from '@/lib/utils';

const StatBox = ({ label, value, sub, icon: Icon, color = 'green' }) => {
    const colors = {
        green: 'text-green-600 bg-green-50',
        blue: 'text-blue-600 bg-blue-50',
        purple: 'text-purple-600 bg-purple-50',
        orange: 'text-orange-600 bg-orange-50',
    };
    return (
        <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div className="flex items-center gap-3">
                <div className={cn('w-9 h-9 rounded-xl flex items-center justify-center shrink-0', colors[color])}>
                    <Icon className="w-4.5 h-4.5" />
                </div>
                <div>
                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{label}</p>
                    <p className="text-lg font-black text-gray-900 italic leading-none">{value}</p>
                </div>
            </div>
            {sub && <p className="text-[8px] font-bold text-gray-400 mt-2 uppercase tracking-widest">{sub}</p>}
        </div>
    );
};

const ChartCard = ({ title, sub, icon: Icon, children, className }) => (
    <div className={cn("bg-white rounded-2xl border border-gray-100 shadow-sm p-6", className)}>
        <div className="flex items-center justify-between mb-6">
            <div>
                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">{title}</h3>
                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{sub}</p>
            </div>
            <div className="w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center">
                <Icon className="w-4 h-4 text-gray-400" />
            </div>
        </div>
        {children}
    </div>
);

export default function StatistikIndex({ 
    auth, basicStats, genderStats, ageGroups, religionStats, 
    educationStats, jobStats, rtStats, rwStats, mutationStats, recentMutations 
}) {
    return (
        <AuthenticatedLayout user={auth.user} title="Statistik Kependudukan">
            <Head title="Statistik & Demografi - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <div className="bg-gradient-to-r from-blue-600 via-indigo-700 to-indigo-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div className="flex items-center gap-4">
                            <Link href={route('laporan.index')} className="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-all border border-white/10">
                                <ChevronLeft className="w-4 h-4 text-white" />
                            </Link>
                            <div className="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                                <BarChart3 className="w-6 h-6 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">Statistik Desa</h1>
                                <p className="text-indigo-100 font-bold text-[10px] uppercase tracking-widest mt-1 opacity-80">Analisis Demografi Real-time</p>
                            </div>
                        </div>
                        <button onClick={() => window.location.reload()} className="flex items-center px-4 py-3 bg-white text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:scale-105 transition-all">
                            <RefreshCw className="w-3.5 h-3.5 mr-2" /> Refresh Data
                        </button>
                    </div>
                </div>

                {/* ── Summary Stats ── */}
                <Deferred data="basicStats" fallback={<div className="grid grid-cols-2 lg:grid-cols-4 gap-4">{[...Array(4)].map((_,i)=><div key={i} className="h-20 bg-white rounded-2xl animate-pulse border border-gray-100"/>)}</div>}>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <StatBox label="Total Penduduk" value={basicStats?.total_penduduk?.toLocaleString('id-ID')} sub={`${basicStats?.laki_laki} L / ${basicStats?.perempuan} P`} icon={Users} color="green" />
                        <StatBox label="Kepala Keluarga" value={basicStats?.total_kk?.toLocaleString('id-ID')} sub="Terdaftar di database" icon={Building2} color="blue" />
                        <StatBox label="Warga Domisili" value={basicStats?.total_domisili?.toLocaleString('id-ID')} sub="Tinggal di wilayah" icon={Map} color="purple" />
                        <StatBox label="Total Mutasi" value={basicStats?.total_mutasi?.toLocaleString('id-ID')} sub="Riwayat perubahan" icon={TrendingUp} color="orange" />
                    </div>
                </Deferred>

                {/* ── Main Charts Grid ── */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    {/* Gender Distribution */}
                    <Deferred data="genderStats" fallback={<SkeletonChart height="380px" />}>
                        <ChartCard title="Distribusi Gender" sub="Perbandingan Laki-laki & Perempuan" icon={Users}>
                            <GenderPieChart data={genderStats ?? []} />
                        </ChartCard>
                    </Deferred>

                    {/* Age Groups */}
                    <Deferred data="ageGroups" fallback={<SkeletonChart height="380px" />}>
                        <ChartCard title="Kelompok Usia" sub="Piramida penduduk per kategori usia" icon={BarChart3}>
                            <AgeBarChart data={ageGroups ?? {}} />
                        </ChartCard>
                    </Deferred>

                    {/* Mutation Trends */}
                    <Deferred data="mutationStats" fallback={<SkeletonChart height="380px" />}>
                        <ChartCard title="Tren Mutasi" sub="Statistik Kelahiran, Kematian & Pindah" icon={TrendingUp} className="lg:col-span-2">
                            <MutasiBarChart data={mutationStats ?? {}} />
                        </ChartCard>
                    </Deferred>

                    {/* More Detailed Stats Cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:col-span-2">
                        
                        {/* Pendidikan */}
                        <Deferred data="educationStats" fallback={<div className="h-48 bg-white rounded-2xl animate-pulse" />}>
                            <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <GraduationCap className="w-3.5 h-3.5" /> Pendidikan Terakhir
                                </h4>
                                <div className="space-y-3">
                                    {(educationStats ?? []).slice(0, 5).map((item, i) => (
                                        <div key={i}>
                                            <div className="flex justify-between text-[10px] font-black uppercase tracking-tighter mb-1">
                                                <span>{item.pendidikan || 'TIDAK SEKOLAH'}</span>
                                                <span className="text-blue-600">{item.total}</span>
                                            </div>
                                            <div className="h-1.5 bg-gray-50 rounded-full overflow-hidden">
                                                <div className="h-full bg-blue-500 rounded-full" style={{ width: `${Math.min(100, (item.total / basicStats.total_penduduk) * 100)}%` }} />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </Deferred>

                        {/* Pekerjaan */}
                        <Deferred data="jobStats" fallback={<div className="h-48 bg-white rounded-2xl animate-pulse" />}>
                            <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <Briefcase className="w-3.5 h-3.5" /> Pekerjaan Utama
                                </h4>
                                <div className="space-y-3">
                                    {(jobStats ?? []).slice(0, 5).map((item, i) => (
                                        <div key={i}>
                                            <div className="flex justify-between text-[10px] font-black uppercase tracking-tighter mb-1">
                                                <span>{item.pekerjaan || 'TIDAK BEKERJA'}</span>
                                                <span className="text-green-600">{item.total}</span>
                                            </div>
                                            <div className="h-1.5 bg-gray-50 rounded-full overflow-hidden">
                                                <div className="h-full bg-green-500 rounded-full" style={{ width: `${Math.min(100, (item.total / basicStats.total_penduduk) * 100)}%` }} />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </Deferred>

                    </div>

                </div>

                {/* ── Territorial Stats ── */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <ChartCard title="Statistik RT" sub="Distribusi warga per RT" icon={Map} className="lg:col-span-1">
                        <div className="max-h-[300px] overflow-y-auto pr-2 space-y-4">
                            {(rtStats ?? []).map((rt, i) => (
                                <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <div className="flex items-center gap-3">
                                        <div className="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-[10px] font-black text-gray-500 shadow-sm">{rt.rt_label}</div>
                                        <span className="text-[10px] font-black text-gray-900 uppercase">RT {rt.rt_label}</span>
                                    </div>
                                    <span className="text-xs font-black text-blue-600">{rt.total} Jiwa</span>
                                </div>
                            ))}
                        </div>
                    </ChartCard>

                    <ChartCard title="Statistik RW" sub="Distribusi warga per RW" icon={Building2} className="lg:col-span-1">
                         <div className="max-h-[300px] overflow-y-auto pr-2 space-y-4">
                            {(rwStats ?? []).map((rw, i) => (
                                <div key={i} className="flex items-center justify-between p-3 bg-indigo-50/50 rounded-xl">
                                    <div className="flex items-center gap-3">
                                        <div className="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-[10px] font-black text-indigo-500 shadow-sm">{rw.rw_label}</div>
                                        <span className="text-[10px] font-black text-gray-900 uppercase">RW {rw.rw_label}</span>
                                    </div>
                                    <span className="text-xs font-black text-indigo-600">{rw.total} Jiwa</span>
                                </div>
                            ))}
                        </div>
                    </ChartCard>

                    <ChartCard title="Agama" sub="Keyakinan penduduk" icon={Heart} className="lg:col-span-1">
                         <div className="space-y-4">
                            {(religionStats ?? []).map((rel, i) => (
                                <div key={i}>
                                    <div className="flex justify-between text-[10px] font-black uppercase tracking-tighter mb-1">
                                        <span>{rel.agama || 'LAINNYA'}</span>
                                        <span className="text-gray-900">{rel.total}</span>
                                    </div>
                                    <div className="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div className={cn("h-full rounded-full", i === 0 ? "bg-green-500" : "bg-gray-400")} style={{ width: `${Math.min(100, (rel.total / basicStats.total_penduduk) * 100)}%` }} />
                                    </div>
                                </div>
                            ))}
                        </div>
                    </ChartCard>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}

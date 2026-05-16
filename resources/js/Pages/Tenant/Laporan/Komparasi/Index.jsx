import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KomparasiLineChart from '@/Components/Laporan/KomparasiLineChart';
import SkeletonChart from '@/Components/Shared/Skeleton/SkeletonChart';
import { 
    Activity, ChevronLeft, Calendar, ArrowRight, 
    TrendingUp, TrendingDown, Minus, Users, GitBranch, FileText
} from 'lucide-react';
import { cn } from '@/lib/utils';

const ComparisonBox = ({ label, icon: Icon, p1, p2, color = 'green' }) => {
    const diff = p2 - p1;
    const pct = p1 > 0 ? ((diff / p1) * 100).toFixed(1) : (p2 > 0 ? '100' : '0');
    
    return (
        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div className="flex items-center justify-between mb-4">
                <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center", 
                    color === 'green' ? 'bg-green-50 text-green-600' : 
                    color === 'blue' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'
                )}>
                    <Icon className="w-5 h-5" />
                </div>
                <div className={cn("flex items-center gap-1 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg", 
                    diff > 0 ? 'bg-green-50 text-green-600' : 
                    diff < 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-500'
                )}>
                    {diff > 0 ? <TrendingUp className="w-3 h-3" /> : diff < 0 ? <TrendingDown className="w-3 h-3" /> : <Minus className="w-3 h-3" />}
                    {Math.abs(pct)}%
                </div>
            </div>
            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{label}</p>
            <div className="flex items-baseline gap-2">
                <span className="text-2xl font-black text-gray-900 italic tracking-tighter">{p2}</span>
                <span className="text-[10px] font-bold text-gray-400">vs {p1}</span>
            </div>
        </div>
    );
};

export default function KomparasiIndex({ auth, comparison, trends, filters }) {
    const [localFilters, setLocalFilters] = React.useState(filters);

    const handleFilterChange = () => {
        router.get(route('comparison.index'), localFilters, { preserveState: true });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Komparasi Data">
            <Head title="Komparasi & Tren - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <div className="bg-gradient-to-r from-purple-600 via-fuchsia-700 to-fuchsia-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center gap-4">
                            <Link href={route('laporan.index')} className="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-all border border-white/10">
                                <ChevronLeft className="w-4 h-4 text-white" />
                            </Link>
                            <div className="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                                <Activity className="w-6 h-6 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">Komparasi Data</h1>
                                <p className="text-fuchsia-100 font-bold text-[10px] uppercase tracking-widest mt-1 opacity-80">Perbandingan Periode & Analisis Tren</p>
                            </div>
                        </div>
                        
                        {/* Period Selectors */}
                        <div className="flex flex-wrap items-center gap-3">
                            <div className="flex items-center bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-1">
                                <input type="month" value={localFilters.month1} onChange={e => setLocalFilters(p => ({...p, month1: e.target.value}))} className="bg-transparent border-none text-white text-[10px] font-black uppercase tracking-widest focus:ring-0 cursor-pointer w-32" />
                                <div className="px-2 text-white/50"><ArrowRight className="w-3 h-3" /></div>
                                <input type="month" value={localFilters.month2} onChange={e => setLocalFilters(p => ({...p, month2: e.target.value}))} className="bg-transparent border-none text-white text-[10px] font-black uppercase tracking-widest focus:ring-0 cursor-pointer w-32" />
                            </div>
                            <button onClick={handleFilterChange} className="px-4 py-2.5 bg-white text-fuchsia-700 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:scale-105 transition-all">
                                Bandingkan
                            </button>
                        </div>
                    </div>
                </div>

                {/* ── Comparison Boxes ── */}
                <Deferred data="comparison" fallback={<div className="grid grid-cols-1 md:grid-cols-3 gap-6">{[...Array(3)].map((_,i)=><div key={i} className="h-32 bg-white rounded-2xl animate-pulse"/>)}</div>}>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <ComparisonBox 
                            label={`Pertumbuhan Penduduk (${comparison?.period2?.label})`}
                            icon={Users}
                            p1={comparison?.period1?.penduduk}
                            p2={comparison?.period2?.penduduk}
                            color="green"
                        />
                        <ComparisonBox 
                            label={`Aktivitas Mutasi (${comparison?.period2?.label})`}
                            icon={GitBranch}
                            p1={comparison?.period1?.mutasi}
                            p2={comparison?.period2?.mutasi}
                            color="blue"
                        />
                        <ComparisonBox 
                            label={`Layanan Surat (${comparison?.period2?.label})`}
                            icon={FileText}
                            p1={comparison?.period1?.surat}
                            p2={comparison?.period2?.surat}
                            color="purple"
                        />
                    </div>
                </Deferred>

                {/* ── Trend Analysis ── */}
                <Deferred data="trends" fallback={<SkeletonChart height="450px" />}>
                    <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 lg:p-8">
                        <div className="flex items-center justify-between mb-8">
                            <div>
                                <h3 className="text-base sm:text-lg font-black text-gray-900 uppercase italic tracking-tighter">Analisis Tren 12 Bulan</h3>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Pergerakan data penduduk & mutasi tahun terakhir</p>
                            </div>
                            <div className="flex items-center gap-4">
                                <div className="flex items-center gap-2">
                                    <div className="w-3 h-3 rounded-full bg-green-500" />
                                    <span className="text-[9px] font-black text-gray-500 uppercase tracking-widest">Penduduk Baru</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <div className="w-3 h-3 rounded-full bg-blue-500" />
                                    <span className="text-[9px] font-black text-gray-500 uppercase tracking-widest">Total Mutasi</span>
                                </div>
                            </div>
                        </div>
                        
                        <KomparasiLineChart data={trends?.data ?? []} />
                        
                        <div className="mt-8 pt-8 border-t border-gray-50 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div className="space-y-1">
                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Rata-rata Penduduk/Bulan</p>
                                <p className="text-xl font-black text-gray-900 italic">
                                    {Math.round((trends?.data ?? []).reduce((acc, curr) => acc + curr.penduduk, 0) / 12)}
                                </p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Puncak Mutasi</p>
                                <p className="text-xl font-black text-blue-600 italic">
                                    {Math.max(...(trends?.data ?? []).map(d => d.mutasi), 0)}
                                </p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Stabilitas Data</p>
                                <p className="text-xl font-black text-green-600 italic">Tinggi</p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Status Analisis</p>
                                <p className="text-[10px] font-black px-2 py-1 bg-green-50 text-green-600 rounded-lg inline-block uppercase tracking-widest border border-green-100">
                                    Optimized
                                </p>
                            </div>
                        </div>
                    </div>
                </Deferred>

            </div>
        </AuthenticatedLayout>
    );
}

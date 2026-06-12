import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KomparasiLineChart from '@/Components/Laporan/KomparasiLineChart';
import SkeletonChart from '@/Components/Shared/Skeleton/SkeletonChart';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import { 
    Activity, ChevronLeft, Calendar, ArrowRight, 
    TrendingUp, TrendingDown, Minus, Users, GitBranch, FileText
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { PageHeader, FilterContainer } from '@/Components/Shared';

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
                <PageHeader 
                    icon={Activity}
                    title="Komparasi Data"
                    subtitle="Perbandingan Periode & Analisis Tren"
                    backHref={route('laporan.index')}
                />

                {/* ── Period Filter ── */}
                <FilterContainer 
                    title="Filter Periode" 
                    subtitle="Pilih 2 bulan untuk dibandingkan" 
                    hasActiveFilters={true}
                >
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div className="flex flex-col sm:flex-row items-end gap-4">
                            <div className="flex-1 w-full">
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Bulan 1 (Pembanding)</label>
                                <input type="month" value={localFilters.month1} onChange={e => setLocalFilters(p => ({...p, month1: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" />
                            </div>
                            <div className="hidden sm:flex items-center justify-center pb-3 px-2">
                                <ArrowRight className="w-4 h-4 text-gray-300" />
                            </div>
                            <div className="flex-1 w-full">
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Bulan 2 (Target)</label>
                                <input type="month" value={localFilters.month2} onChange={e => setLocalFilters(p => ({...p, month2: e.target.value}))} className="w-full px-3 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700" />
                            </div>
                            <button onClick={handleFilterChange} className="w-full sm:w-auto px-6 py-2.5 bg-fuchsia-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-fuchsia-700 transition-all shadow-md mt-4 sm:mt-0">
                                Bandingkan Data
                            </button>
                        </div>
                    </div>
                </FilterContainer>

                {/* ── Comparison Boxes ── */}
                <Deferred data="comparison" fallback={<SkeletonStats count={3} />}>
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

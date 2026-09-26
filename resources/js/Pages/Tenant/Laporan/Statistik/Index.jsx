import React, { useState } from 'react';
import { Head, Link, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PageHeader from '@/Components/Shared/PageHeader';
import GenderPieChart from '@/Components/Laporan/GenderPieChart';
import AgeBarChart from '@/Components/Laporan/AgeBarChart';
import MutasiBarChart from '@/Components/Laporan/MutasiBarChart';
import SkeletonChart from '@/Components/Shared/Skeleton/SkeletonChart';
import { 
    BarChart3, Users, Baby, Skull, TrendingUp, ChevronLeft, 
    PieChart, Map, Building2, Briefcase, GraduationCap, Heart, Download,
    RefreshCw, Home, Layers, Compass
} from 'lucide-react';
import { cn } from '@/lib/utils';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Cell, PieChart as RechartsPieChart, Pie, Legend } from 'recharts';

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

const HorizontalBarChart = ({ data, dataKey = "total", nameKey = "name" }) => (
    <div style={{ width: '100%', height: '280px' }}>
        <ResponsiveContainer>
            <BarChart layout="vertical" data={data} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                <CartesianGrid strokeDasharray="3 3" horizontal={false} opacity={0.3} />
                <XAxis type="number" />
                <YAxis dataKey={nameKey} type="category" width={140} tick={{fontSize: 10, fill: '#6b7280', fontWeight: 'bold'}} />
                <Tooltip cursor={{fill: '#f3f4f6'}} contentStyle={{borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)'}} />
                <Bar dataKey={dataKey} radius={[0, 4, 4, 0]} barSize={24}>
                    {data.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                </Bar>
            </BarChart>
        </ResponsiveContainer>
    </div>
);

const SimplePieChart = ({ data, dataKey = "total", nameKey = "name" }) => (
    <div style={{ width: '100%', height: '280px' }}>
        <ResponsiveContainer>
            <RechartsPieChart>
                <Pie
                    data={data}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={80}
                    paddingAngle={5}
                    dataKey={dataKey}
                    nameKey={nameKey}
                    label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                    labelLine={false}
                    style={{fontSize: '10px', fontWeight: 'bold', fill: '#4b5563'}}
                >
                    {data.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                </Pie>
                <Legend verticalAlign="bottom" height={36} iconType="circle" wrapperStyle={{ fontSize: '10px', fontWeight: 'bold', color: '#4b5563' }} />
                <Tooltip contentStyle={{borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)'}} />
            </RechartsPieChart>
        </ResponsiveContainer>
    </div>
);

const StatBox = ({ label, value, sub, icon: Icon, color = 'green' }) => {
    const colors = {
        green: 'text-green-600 bg-green-50',
        blue: 'text-blue-600 bg-blue-50',
        purple: 'text-purple-600 bg-purple-50',
        orange: 'text-orange-600 bg-orange-50',
        indigo: 'text-indigo-600 bg-indigo-50',
        emerald: 'text-emerald-600 bg-emerald-50',
    };
    return (
        <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
            <div className="flex items-center gap-3">
                <div className={cn('w-9 h-9 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform', colors[color] || colors.green)}>
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
    <div className={cn("bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow", className)}>
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
    educationStats, jobStats, rtStats, rwStats, territorialAverages, rwBreakdown, mutationStats, recentMutations 
}) {
    const [isExporting, setIsExporting] = useState(false);

    const exportToPDF = async () => {
        setIsExporting(true);
        try {
            const element = document.getElementById('statistik-container');
            const canvas = await html2canvas(element, { scale: 2, useCORS: true });
            const imgData = canvas.toDataURL('image/png');
            
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save('Laporan_Statistik_Kependudukan.pdf');
        } catch (error) {
            console.error('Failed to export PDF', error);
            alert('Gagal mengexport PDF. Silakan coba lagi.');
        } finally {
            setIsExporting(false);
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Statistik Kependudukan">
            <Head title="Statistik & Demografi - Admin Panel" />

            <div id="statistik-container" className="space-y-6 animate-in fade-in duration-700 pb-20">

                <PageHeader 
                    icon={PieChart}
                    title="Statistik Desa"
                    subtitle="Analisis Demografi Real-time"
                    backHref={route('laporan.index')}
                    actions={[
                        {
                            label: isExporting ? 'MENYIAPKAN...' : 'EXPORT PDF',
                            icon: Download,
                            onClick: exportToPDF,
                            disabled: isExporting,
                            variant: 'white'
                        },
                        {
                            label: 'Refresh Data',
                            icon: RefreshCw,
                            onClick: () => window.location.reload(),
                            variant: 'ghost'
                        }
                    ]}
                />

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
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:col-span-2">
                        
                        {/* Pendidikan */}
                        <Deferred data="educationStats" fallback={<SkeletonChart height="320px" />}>
                            <ChartCard title="Pendidikan Terakhir" sub="Tingkat Pendidikan Warga" icon={GraduationCap}>
                                <HorizontalBarChart 
                                    data={(educationStats ?? []).slice(0, 5).map(item => ({
                                        name: item.pendidikan || 'TIDAK SEKOLAH',
                                        total: item.total
                                    }))} 
                                />
                            </ChartCard>
                        </Deferred>

                        {/* Pekerjaan */}
                        <Deferred data="jobStats" fallback={<SkeletonChart height="320px" />}>
                            <ChartCard title="Pekerjaan Utama" sub="Top 5 Jenis Pekerjaan" icon={Briefcase}>
                                <HorizontalBarChart 
                                    data={(jobStats ?? []).slice(0, 5).map(item => ({
                                        name: item.pekerjaan || 'TIDAK BEKERJA',
                                        total: item.total
                                    }))} 
                                />
                            </ChartCard>
                        </Deferred>

                    </div>

                </div>

                {/* ── Territorial & Regional Averages Section ── */}
                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <div>
                            <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Analisis Sebaran & Rata-rata Wilayah</h2>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Statistik Kepadatan Penduduk & Kepala Keluarga per RT / RW</p>
                        </div>
                    </div>

                    <Deferred data="territorialAverages" fallback={<div className="grid grid-cols-2 lg:grid-cols-4 gap-4">{[...Array(4)].map((_,i)=><div key={i} className="h-20 bg-white rounded-2xl animate-pulse border border-gray-100"/>)}</div>}>
                        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <StatBox 
                                label="Rata-rata Jiwa / RT" 
                                value={`${territorialAverages?.avg_penduduk_per_rt ?? 0} Jiwa`} 
                                sub={`Total ${territorialAverages?.total_rt ?? 0} RT terdata`} 
                                icon={Map} 
                                color="blue" 
                            />
                            <StatBox 
                                label="Rata-rata Jiwa / RW" 
                                value={`${territorialAverages?.avg_penduduk_per_rw ?? 0} Jiwa`} 
                                sub={`Total ${territorialAverages?.total_rw ?? 0} RW terdata`} 
                                icon={Building2} 
                                color="indigo" 
                            />
                            <StatBox 
                                label="Rata-rata KK / RT" 
                                value={`${territorialAverages?.avg_kk_per_rt ?? 0} KK`} 
                                sub={`Kepadatan per unit RT`} 
                                icon={Home} 
                                color="emerald" 
                            />
                            <StatBox 
                                label="Ukuran Keluarga" 
                                value={`${territorialAverages?.avg_jiwa_per_kk ?? 0} Jiwa`} 
                                sub={`Rata-rata jiwa per KK`} 
                                icon={Users} 
                                color="purple" 
                            />
                        </div>
                    </Deferred>
                </div>

                {/* ── RW Breakdown Table / Overview ── */}
                <Deferred data="rwBreakdown" fallback={<div className="h-48 bg-white rounded-2xl animate-pulse border border-gray-100"/>}>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div className="flex items-center justify-between mb-4">
                            <div>
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Komparasi Antar Rukun Warga (RW)</h3>
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Rincian Jumlah RT, Total Warga, Total KK, dan Rata-rata per RW</p>
                            </div>
                            <div className="w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center">
                                <Layers className="w-4 h-4 text-gray-400" />
                            </div>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead>
                                    <tr className="border-b border-gray-100 text-[10px] font-black uppercase text-gray-400 tracking-wider">
                                        <th className="pb-3 pr-4">Rukun Warga</th>
                                        <th className="pb-3 px-4">Dusun</th>
                                        <th className="pb-3 px-4 text-center">Jumlah RT</th>
                                        <th className="pb-3 px-4 text-right">Total Penduduk</th>
                                        <th className="pb-3 px-4 text-right">Total KK</th>
                                        <th className="pb-3 px-4 text-right">Rata-rata Jiwa / RT</th>
                                        <th className="pb-3 pl-4 text-right">Porsi Wilayah</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {(rwBreakdown ?? []).map((rw, idx) => (
                                        <tr key={idx} className="hover:bg-gray-50/80 transition-colors">
                                            <td className="py-3.5 pr-4 font-black text-gray-900">
                                                <div className="flex items-center gap-2">
                                                    <span className="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 font-black text-[10px] flex items-center justify-center">
                                                        {rw.kode}
                                                    </span>
                                                    <span>RW {rw.kode}</span>
                                                </div>
                                            </td>
                                            <td className="py-3.5 px-4 text-gray-500 font-bold uppercase text-[11px]">{rw.dusun || '-'}</td>
                                            <td className="py-3.5 px-4 text-center font-black text-gray-700">{rw.total_rt} RT</td>
                                            <td className="py-3.5 px-4 text-right font-black text-gray-900">{rw.total_penduduk?.toLocaleString('id-ID')} Jiwa</td>
                                            <td className="py-3.5 px-4 text-right font-black text-gray-600">{rw.total_kk?.toLocaleString('id-ID')} KK</td>
                                            <td className="py-3.5 px-4 text-right font-black text-blue-600">{rw.avg_penduduk_per_rt} Jiwa / RT</td>
                                            <td className="py-3.5 pl-4 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <div className="w-16 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                        <div className="bg-indigo-600 h-full rounded-full" style={{ width: `${Math.min(rw.persentase || 0, 100)}%` }} />
                                                    </div>
                                                    <span className="font-bold text-gray-500 text-[10px] w-9">{rw.persentase}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Deferred>

                {/* ── Territorial Lists & Religion ── */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <ChartCard title="Distribusi RT" sub="Daftar warga per RT" icon={Map} className="lg:col-span-1">
                        <Deferred data="rtStats" fallback={<div className="h-64 bg-gray-50 rounded-xl animate-pulse" />}>
                            <div className="max-h-[320px] overflow-y-auto pr-2 space-y-3">
                                {(rtStats ?? []).map((rt, i) => {
                                    const label = rt.label || rt.rt_kode || rt.rt_label || (i + 1);
                                    return (
                                        <div key={i} className="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors rounded-xl border border-gray-100">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-[10px] font-black text-gray-600 shadow-sm border border-gray-100">{label}</div>
                                                <div>
                                                    <span className="text-[10px] font-black text-gray-900 uppercase block">RT {label}</span>
                                                    {rt.rw_kode && <span className="text-[9px] font-bold text-gray-400">RW {rt.rw_kode}</span>}
                                                </div>
                                            </div>
                                            <span className="text-xs font-black text-blue-600">{rt.total} Jiwa</span>
                                        </div>
                                    );
                                })}
                            </div>
                        </Deferred>
                    </ChartCard>

                    <ChartCard title="Distribusi RW" sub="Daftar warga per RW" icon={Building2} className="lg:col-span-1">
                        <Deferred data="rwStats" fallback={<div className="h-64 bg-gray-50 rounded-xl animate-pulse" />}>
                            <div className="max-h-[320px] overflow-y-auto pr-2 space-y-3">
                                {(rwStats ?? []).map((rw, i) => {
                                    const label = rw.label || rw.rw_kode || rw.rw_label || (i + 1);
                                    return (
                                        <div key={i} className="flex items-center justify-between p-3 bg-indigo-50/50 hover:bg-indigo-100/50 transition-colors rounded-xl border border-indigo-100/50">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-[10px] font-black text-indigo-500 shadow-sm border border-indigo-50">{label}</div>
                                                <div>
                                                    <span className="text-[10px] font-black text-gray-900 uppercase block">RW {label}</span>
                                                    {rw.avg_per_rt && <span className="text-[9px] font-bold text-indigo-400">~{rw.avg_per_rt} Jiwa/RT</span>}
                                                </div>
                                            </div>
                                            <span className="text-xs font-black text-indigo-600">{rw.total} Jiwa</span>
                                        </div>
                                    );
                                })}
                            </div>
                        </Deferred>
                    </ChartCard>

                    <ChartCard title="Agama" sub="Keyakinan penduduk" icon={Heart} className="lg:col-span-1">
                        <Deferred data="religionStats" fallback={<SkeletonChart height="250px" />}>
                            <SimplePieChart 
                                data={(religionStats ?? []).map(rel => ({
                                    name: rel.agama || 'LAINNYA',
                                    total: rel.total
                                }))} 
                            />
                        </Deferred>
                    </ChartCard>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}

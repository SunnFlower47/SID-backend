import React from 'react';
import { TrendingUp, Wallet, ArrowDownCircle, ArrowUpCircle, Building2, CheckCircle, Clock, BarChart3 } from 'lucide-react';
import { cn } from '@/lib/utils';

const formatRupiah = (value) => {
    if (!value && value !== 0) return '0';
    if (value >= 1_000_000_000) return `${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `${(value / 1_000_000).toFixed(1)} Jt`;
    return value.toLocaleString('id-ID');
};

export default function KeuanganStats({ stats = {} }) {
    const pctSerap = stats.total_anggaran > 0
        ? Math.min(100, Math.round((stats.total_realisasi / stats.total_anggaran) * 100))
        : 0;

    const statCards = [
        {
            title: 'Total Anggaran',
            value: `Rp ${formatRupiah(stats.total_anggaran)}`,
            icon: Wallet,
            color: 'blue',
            sub: `Tahun ${new Date().getFullYear()}`,
        },
        {
            title: 'Total Realisasi',
            value: `Rp ${formatRupiah(stats.total_realisasi)}`,
            icon: TrendingUp,
            color: 'emerald',
            sub: `${pctSerap}% terserap`,
        },
        {
            title: 'Proyek Aktif',
            value: stats.proyek_aktif ?? 0,
            icon: Building2,
            color: 'orange',
            sub: `${stats.total_proyek ?? 0} total proyek`,
        },
        {
            title: 'Proyek Selesai',
            value: stats.proyek_selesai ?? 0,
            icon: CheckCircle,
            color: 'purple',
            sub: `dari ${stats.total_proyek ?? 0} proyek`,
        },
    ];

    const colorClasses = {
        blue:    'border-blue-100 bg-blue-50 text-blue-600 shadow-blue-100/50',
        emerald: 'border-emerald-100 bg-emerald-50 text-emerald-600 shadow-emerald-100/50',
        orange:  'border-orange-100 bg-orange-50 text-orange-600 shadow-orange-100/50',
        purple:  'border-purple-100 bg-purple-50 text-purple-600 shadow-purple-100/50',
    };

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {statCards.map((stat, index) => {
                const Icon = stat.icon;
                const colors = colorClasses[stat.color] || colorClasses.blue;
                const [borderCls, bgCls, textCls, shadowCls] = colors.split(' ');

                return (
                    <div
                        key={index}
                        className={cn(
                            'bg-white rounded-2xl p-3 sm:p-4 border shadow-sm hover:shadow-md transition-all',
                            borderCls,
                            shadowCls
                        )}
                    >
                        <div className="flex items-center gap-3 sm:gap-4">
                            <div className={cn('w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0', bgCls, textCls)}>
                                <Icon className="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">
                                    {stat.title}
                                </p>
                                <h3 className="text-lg sm:text-xl font-black text-gray-900 leading-none tracking-tighter italic">
                                    {typeof stat.value === 'number' ? stat.value.toLocaleString('id-ID') : stat.value}
                                </h3>
                                {stat.sub && (
                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{stat.sub}</p>
                                )}
                            </div>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

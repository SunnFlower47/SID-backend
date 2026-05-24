import React from 'react';
import { cn } from '@/lib/utils';

/**
 * StatCard — Kartu statistik ringkasan yang dipakai di Dashboard, Laporan, Keuangan, dsb.
 *
 * @param {React.ElementType} icon    - Lucide icon component (bukan element)
 * @param {string}            label   - Label/nama metrik kecil di bawah nilai
 * @param {string|number}     value   - Nilai utama yang ditampilkan besar
 * @param {string}            color   - Warna tema: green|blue|purple|orange|rose|teal|yellow|emerald|gray
 * @param {number|null}       trend   - Angka positif/negatif untuk badge trend hari ini (opsional)
 * @param {string}            trendLabel - Label trend, default 'hari ini'
 * @param {string}            sub     - Teks kecil tambahan di bawah label (opsional)
 * @param {string}            badge   - Badge teks di pojok kanan atas (misal: 'Live', 'Aktif', 'Total')
 * @param {string}            className - Override class container
 *
 * Kenapa ada dua varian (badge vs trend)?
 * - Dashboard/Index pakai 'badge' statis (teks: "Live", "Aktif")
 * - Laporan/Dashboard pakai 'trend' dinamis (angka + icon TrendingUp/TrendingDown)
 * Keduanya di-support supaya satu komponen bisa handle semua kasus.
 */

const COLOR_MAP = {
    green:   { bg: 'bg-green-50',   icon: 'text-green-600',   border: 'border-green-100'   },
    blue:    { bg: 'bg-blue-50',    icon: 'text-blue-600',    border: 'border-blue-100'    },
    purple:  { bg: 'bg-purple-50',  icon: 'text-purple-600',  border: 'border-purple-100'  },
    orange:  { bg: 'bg-orange-50',  icon: 'text-orange-600',  border: 'border-orange-100'  },
    rose:    { bg: 'bg-rose-50',    icon: 'text-rose-600',    border: 'border-rose-100'    },
    teal:    { bg: 'bg-teal-50',    icon: 'text-teal-600',    border: 'border-teal-100'    },
    yellow:  { bg: 'bg-yellow-50',  icon: 'text-yellow-600',  border: 'border-yellow-100'  },
    emerald: { bg: 'bg-emerald-50', icon: 'text-emerald-600', border: 'border-emerald-100' },
    gray:    { bg: 'bg-gray-50',    icon: 'text-gray-500',    border: 'border-gray-100'    },
};

export default function StatCard({
    icon: Icon,
    label,
    title,
    value,
    color = 'green',
    trend,
    trendLabel = 'hari ini',
    sub,
    badge,
    className = '',
    compact = false,
}) {
    const c = COLOR_MAP[color] ?? COLOR_MAP.green;
    const displayLabel = label ?? title;

    if (compact) {
        return (
            <div className={cn(
                'bg-white rounded-xl border shadow-sm p-2 sm:p-2.5 hover:shadow-md transition-all flex items-center justify-between gap-2 min-w-0',
                c.border,
                className
            )}>
                <div className="flex items-center gap-2 min-w-0">
                    {/* Icon box */}
                    <div className={cn('w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border border-transparent', c.bg)}>
                        {Icon && <Icon className={cn('w-4 h-4', c.icon)} />}
                    </div>

                    {/* Label & Value */}
                    <div className="min-w-0 text-left">
                        <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">
                            {displayLabel}
                        </p>
                        <h2 className="text-sm sm:text-base font-black text-gray-950 mt-1 tracking-tight leading-none italic">
                            {value ?? 0}
                        </h2>
                        {sub && (
                            <p className="text-[7px] text-gray-400 font-bold mt-0.5 leading-none">{sub}</p>
                        )}
                    </div>
                </div>

                {/* Badge statis ATAU trend dinamis di kanan */}
                <div className="shrink-0 flex flex-col items-end gap-1">
                    {badge && (
                        <span className="px-1 py-0.5 bg-white border border-gray-100 rounded-md text-[6.5px] font-black text-gray-400 uppercase tracking-widest shadow-sm">
                            {badge}
                        </span>
                    )}
                    {trend != null && (
                        <span className={cn(
                            'text-[8px] font-black uppercase tracking-widest flex items-center gap-0.5',
                            trend > 0 ? 'text-green-500' : trend < 0 ? 'text-red-400' : 'text-gray-400'
                        )}>
                            {trend > 0 && (
                                <svg className="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            )}
                            {trend < 0 && (
                                <svg className="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            )}
                            {Math.abs(trend)}
                        </span>
                    )}
                </div>
            </div>
        );
    }

    return (
        <div className={cn(
            'bg-white rounded-2xl border shadow-sm p-3 hover:shadow-md transition-all flex items-center justify-between gap-3 min-w-0 text-left w-full h-full',
            c.border,
            className
        )}>
            <div className="flex items-center gap-3 min-w-0">
                {/* Icon box */}
                <div className={cn('w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center shrink-0 border border-transparent', c.bg)}>
                    {Icon && <Icon className={cn('w-4 h-4 sm:w-4.5 sm:h-4.5', c.icon)} />}
                </div>

                {/* Label & Value */}
                <div className="min-w-0 text-left flex flex-col justify-center">
                    <p className="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest truncate leading-tight mb-0.5 text-left">
                        {displayLabel}
                    </p>
                    <h3 className="text-base sm:text-lg font-black text-gray-950 leading-none text-left">
                        {value ?? 0}
                    </h3>
                    <div className="min-h-[12px] mt-0.5">
                        {sub && (
                            <p className="text-[7px] sm:text-[8px] text-gray-400 font-bold leading-none text-left">{sub}</p>
                        )}
                    </div>
                </div>
            </div>

            {/* Badge statis ATAU trend dinamis di kanan */}
            <div className="shrink-0 flex flex-col items-end gap-1">
                {badge && (
                    <span className="px-1.5 py-0.5 bg-white border border-gray-100 rounded-md text-[7px] font-black text-gray-400 uppercase tracking-widest shadow-sm">
                        {badge}
                    </span>
                )}
                {trend != null && (
                    <span className={cn(
                        'text-[8px] font-black uppercase tracking-widest flex items-center gap-0.5',
                        trend > 0 ? 'text-green-500' : trend < 0 ? 'text-red-400' : 'text-gray-400'
                    )}>
                        {trend > 0 && (
                            <svg className="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        )}
                        {trend < 0 && (
                            <svg className="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        )}
                        {trend !== 0 ? `${Math.abs(trend)} ${trendLabel}` : `Tidak ada ${trendLabel}`}
                    </span>
                )}
            </div>
        </div>
    );
}

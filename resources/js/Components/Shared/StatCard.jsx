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
    value,
    color = 'green',
    trend,
    trendLabel = 'hari ini',
    sub,
    badge,
    className = '',
}) {
    const c = COLOR_MAP[color] ?? COLOR_MAP.green;

    return (
        <div className={cn(
            'bg-white rounded-2xl border shadow-sm p-4 sm:p-5 hover:shadow-md transition-all',
            c.border,
            className
        )}>
            <div className="flex items-start justify-between mb-3">
                {/* Icon box */}
                <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center shrink-0', c.bg)}>
                    {Icon && <Icon className={cn('w-5 h-5', c.icon)} />}
                </div>

                {/* Badge statis ATAU trend dinamis di pojok kanan */}
                {badge && (
                    <span className="px-2 py-0.5 bg-white border border-gray-100 rounded-lg text-[8px] font-black text-gray-400 uppercase tracking-widest shadow-sm">
                        {badge}
                    </span>
                )}
                {trend != null && (
                    <span className={cn(
                        'text-[9px] font-black uppercase tracking-widest flex items-center gap-0.5',
                        trend > 0 ? 'text-green-500' : trend < 0 ? 'text-red-400' : 'text-gray-400'
                    )}>
                        {trend > 0 && (
                            <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        )}
                        {trend < 0 && (
                            <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        )}
                        {trend !== 0 ? `${Math.abs(trend)} ${trendLabel}` : `Tidak ada ${trendLabel}`}
                    </span>
                )}
            </div>

            {/* Nilai utama */}
            <p className="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] leading-none">
                {label}
            </p>
            <h2 className="text-xl sm:text-2xl font-black text-gray-950 mt-1 tracking-tighter leading-none italic">
                {value ?? 0}
            </h2>

            {/* Sub-label opsional */}
            {sub && (
                <p className="text-[9px] text-gray-400 font-bold mt-1">{sub}</p>
            )}
        </div>
    );
}

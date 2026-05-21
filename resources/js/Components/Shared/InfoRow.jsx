import React from 'react';
import { cn } from '@/lib/utils';

/**
 * InfoRow — Baris info dengan icon yang dipakai di semua Show pages (Detail).
 * Menggantikan const InfoRow = ... yang didefinisikan lokal di tiap Show page.
 *
 * @param {string}            label     - Label field kecil di atas nilai
 * @param {string|ReactNode}  value     - Nilai yang ditampilkan
 * @param {React.ElementType} icon      - Lucide icon component
 * @param {string}            color     - Warna icon box: blue|green|purple|orange|red|teal|gray (default: blue)
 * @param {string}            className - Override class container
 *
 * Kenapa dipisah dari FormField?
 * InfoRow adalah komponen READ-ONLY (display) bukan input form.
 * Dipakai di Show pages untuk menampilkan data, bukan untuk mengisi data.
 */

const COLOR_MAP = {
    blue:   { bg: 'bg-blue-50',   text: 'text-blue-600',   border: 'border-blue-100'   },
    green:  { bg: 'bg-green-50',  text: 'text-green-600',  border: 'border-green-100'  },
    purple: { bg: 'bg-purple-50', text: 'text-purple-600', border: 'border-purple-100' },
    orange: { bg: 'bg-orange-50', text: 'text-orange-600', border: 'border-orange-100' },
    red:    { bg: 'bg-red-50',    text: 'text-red-600',    border: 'border-red-100'    },
    teal:   { bg: 'bg-teal-50',   text: 'text-teal-600',   border: 'border-teal-100'   },
    gray:   { bg: 'bg-gray-50',   text: 'text-gray-500',   border: 'border-gray-100'   },
};

export default function InfoRow({ label, value, icon: Icon, color = 'blue', className = '' }) {
    const c = COLOR_MAP[color] ?? COLOR_MAP.blue;

    return (
        <div className={cn(
            'flex items-center gap-4 p-4 bg-gray-50/50 rounded-2xl border border-gray-100 hover:bg-white hover:shadow-md transition-all group',
            className
        )}>
            {Icon && (
                <div className={cn(
                    'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border transition-all group-hover:scale-110',
                    c.bg, c.text, c.border
                )}>
                    <Icon className="w-5 h-5" />
                </div>
            )}
            <div className="min-w-0 flex-1">
                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5">
                    {label}
                </p>
                <p className="text-sm font-black text-gray-900 leading-tight uppercase italic truncate">
                    {value || '-'}
                </p>
            </div>
        </div>
    );
}

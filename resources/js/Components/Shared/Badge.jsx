import React from 'react';
import { cn } from '@/lib/utils';

/**
 * Badge — Generic badge yang menggantikan semua StatusBadge, PriorityBadge,
 * JenisBadge, dsb yang didefinisikan ulang di tiap module.
 *
 * @param {string}            color    - Warna badge: green|blue|red|yellow|orange|purple|teal|gray|pink|emerald|rose
 * @param {React.ElementType} icon     - Lucide icon component (opsional)
 * @param {boolean}           pulse    - Animasi pulse (untuk status darurat/urgent)
 * @param {string}            size     - 'sm' | 'md' (default: 'md')
 * @param {string}            dot      - Warna dot bulat kiri: sama format dengan color (opsional, pengganti icon)
 * @param {React.ReactNode}   children - Teks badge
 * @param {string}            className- Override class tambahan
 */

const COLOR_MAP = {
    green:   { bg: 'bg-green-100',   text: 'text-green-800',   border: 'border-green-200',   dot: 'bg-green-500'   },
    blue:    { bg: 'bg-blue-100',    text: 'text-blue-800',    border: 'border-blue-200',    dot: 'bg-blue-500'    },
    red:     { bg: 'bg-red-100',     text: 'text-red-800',     border: 'border-red-200',     dot: 'bg-red-500'     },
    yellow:  { bg: 'bg-yellow-100',  text: 'text-yellow-800',  border: 'border-yellow-200',  dot: 'bg-yellow-500'  },
    orange:  { bg: 'bg-orange-100',  text: 'text-orange-800',  border: 'border-orange-200',  dot: 'bg-orange-500'  },
    purple:  { bg: 'bg-purple-100',  text: 'text-purple-800',  border: 'border-purple-200',  dot: 'bg-purple-500'  },
    teal:    { bg: 'bg-teal-100',    text: 'text-teal-800',    border: 'border-teal-200',    dot: 'bg-teal-500'    },
    gray:    { bg: 'bg-gray-100',    text: 'text-gray-800',    border: 'border-gray-200',    dot: 'bg-gray-400'    },
    pink:    { bg: 'bg-pink-100',    text: 'text-pink-800',    border: 'border-pink-200',    dot: 'bg-pink-500'    },
    emerald: { bg: 'bg-emerald-100', text: 'text-emerald-800', border: 'border-emerald-200', dot: 'bg-emerald-500' },
    rose:    { bg: 'bg-rose-100',    text: 'text-rose-800',    border: 'border-rose-200',    dot: 'bg-rose-500'    },
    indigo:  { bg: 'bg-indigo-100',  text: 'text-indigo-800',  border: 'border-indigo-200',  dot: 'bg-indigo-500'  },
};

export default function Badge({
    color = 'gray',
    icon: Icon,
    pulse = false,
    size = 'md',
    dot,
    children,
    className = '',
}) {
    const c = COLOR_MAP[color] ?? COLOR_MAP.gray;

    const sizeClass = size === 'sm'
        ? 'px-2 py-0.5 text-[9px] gap-1'
        : 'px-2.5 py-1 text-[10px] gap-1.5';

    return (
        <span className={cn(
            'inline-flex items-center rounded-full font-black uppercase tracking-widest border',
            c.bg, c.text, c.border,
            sizeClass,
            className
        )}>
            {/* Dot bulat kecil (alternatif icon) */}
            {dot && (
                <span className={cn(
                    'rounded-full shrink-0',
                    COLOR_MAP[dot]?.dot ?? COLOR_MAP.gray.dot,
                    size === 'sm' ? 'w-1.5 h-1.5' : 'w-2 h-2',
                    pulse && 'animate-pulse'
                )} />
            )}

            {/* Icon */}
            {Icon && !dot && (
                <Icon className={cn('shrink-0', size === 'sm' ? 'w-2.5 h-2.5' : 'w-3 h-3')} />
            )}

            {children}
        </span>
    );
}

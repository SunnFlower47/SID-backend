import React from 'react';
import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

/**
 * PageHeader — Header gradient hijau reusable untuk semua halaman.
 *
 * @param {React.ElementType} icon        - Lucide icon component (bukan element, tapi component: Users bukan <Users/>)
 * @param {string}            title       - Judul utama halaman
 * @param {string}            subtitle    - Subjudul kecil di bawah judul
 * @param {Array}             actions     - Array tombol aksi di kanan header
 *   Tiap action: { label, icon: IconComponent, href?, onClick?, variant?: 'white'|'ghost'|'danger', disabled?, loading? }
 * @param {React.ReactNode}   backHref    - URL untuk tombol "kembali" (ChevronLeft) sebelum icon box — opsional
 * @param {string}            titleSize   - Override ukuran title: 'sm' (text-xl sm:text-2xl) | 'lg' (default: text-xl sm:text-3xl)
 * @param {string}            gradient    - Override gradient class, default 'from-green-600 via-green-700 to-green-800'
 * @param {string}            className   - Override class container luar
 *
 * Kenapa ada titleSize?
 * Hasil audit: Show pages (detail) pakai sm:text-2xl, sedangkan Index/Create/Edit pakai sm:text-3xl.
 * Dashboard/Index pakai md:text-3xl (breakpoint berbeda). Kita standarkan ke sm: karena lebih banyak,
 * tapi expose prop titleSize untuk override.
 */
export default function PageHeader({
    icon: Icon,
    title,
    subtitle,
    actions = [],
    backHref = null,
    titleSize = 'lg',
    gradient = 'from-green-600 via-green-700 to-green-800',
    className = '',
    children,
}) {
    const titleClass = titleSize === 'sm'
        ? 'text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none'
        : 'text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none';

    return (
        <div className={cn(
            `bg-gradient-to-r ${gradient} rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden`,
            className
        )}>
            {/* Dekorasi blur circle */}
            <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />

            <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                {/* Kiri: back button (opsional) + icon box + title */}
                <div className="flex items-center gap-4">
                    {/* Tombol kembali — muncul di Show pages seperti AuditLog/Show */}
                    {backHref && (
                        <Link
                            href={backHref}
                            className="w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-xl flex items-center justify-center border border-white/10 transition-all shrink-0"
                        >
                            {/* ChevronLeft hardcoded agar tidak perlu import di setiap page */}
                            <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                    )}

                    {/* Icon box */}
                    {Icon && (
                        <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                            <Icon className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                        </div>
                    )}

                    {/* Title & subtitle */}
                    <div>
                        <h1 className={titleClass}>{title}</h1>
                        {subtitle && (
                            <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">
                                {subtitle}
                            </p>
                        )}
                    </div>
                </div>

                {/* Kanan: action buttons */}
                {(actions.length > 0 || children) && (
                    <div className="flex flex-wrap items-center gap-2 sm:gap-3 shrink-0">
                        {children}
                        {actions.map((action, i) => {
                            const ActionIcon = action.icon;
                            const baseClass = 'inline-flex items-center px-4 py-3 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-widest transition-all';

                            const variantClass = {
                                white: 'bg-white text-green-700 hover:bg-green-50 shadow-lg shadow-black/10 hover:scale-105',
                                ghost: 'bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white',
                                danger: 'bg-red-500 hover:bg-red-600 text-white shadow-lg shadow-red-900/20',
                            }[action.variant ?? 'ghost'];

                            const disabledClass = (action.disabled || action.loading) ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

                            const content = (
                                <>
                                    {action.loading ? (
                                        <span className="w-3.5 h-3.5 mr-2 border-2 border-current border-t-transparent rounded-full animate-spin" />
                                    ) : ActionIcon ? (
                                        <ActionIcon className="w-3.5 h-3.5 mr-2" />
                                    ) : null}
                                    {action.label}
                                </>
                            );

                            return action.href ? (
                                <Link
                                    key={i}
                                    href={action.href}
                                    className={cn(baseClass, variantClass, disabledClass)}
                                >
                                    {content}
                                </Link>
                            ) : (
                                <button
                                    key={i}
                                    type="button"
                                    onClick={action.onClick}
                                    disabled={action.disabled || action.loading}
                                    className={cn(baseClass, variantClass, disabledClass)}
                                >
                                    {content}
                                </button>
                            );
                        })}
                    </div>
                )}
            </div>
        </div>
    );
}

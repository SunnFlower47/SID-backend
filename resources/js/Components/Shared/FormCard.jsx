import React from 'react';
import { cn } from '@/lib/utils';

/**
 * FormCard — Card wrapper section dalam form dengan header icon + title.
 * Menggantikan pola berulang di semua Create/Edit pages.
 *
 * @param {React.ElementType} icon      - Lucide icon component
 * @param {string}            title     - Judul section form
 * @param {React.ReactNode}   children  - Konten form (fields)
 * @param {React.ReactNode}   actions   - Elemen di kanan header (opsional, misalnya tombol)
 * @param {string}            className - Override class container luar
 * @param {string}            bodyClass - Override class body (area children), default 'p-6 sm:p-8'
 */
export default function FormCard({
    icon: Icon,
    title,
    children,
    actions,
    className = '',
    bodyClass = 'p-6 sm:p-8',
}) {
    return (
        <div className={cn('bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden', className)}>
            {/* Header section */}
            <div className="p-5 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <div className="flex items-center gap-3">
                    {Icon && (
                        <div className="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center shrink-0">
                            <Icon className="w-4 h-4 text-green-600" />
                        </div>
                    )}
                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">
                        {title}
                    </h3>
                </div>
                {actions && (
                    <div className="flex items-center gap-2">
                        {actions}
                    </div>
                )}
            </div>

            {/* Body */}
            <div className={bodyClass}>
                {children}
            </div>
        </div>
    );
}

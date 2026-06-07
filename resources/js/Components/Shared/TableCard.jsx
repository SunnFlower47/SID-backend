import React from 'react';
import { cn } from '@/lib/utils';
import Pagination from '@/Components/Shared/Pagination';

/**
 * TableCard — Wrapper container kartu tabel dengan header section + pagination footer.
 *
 * @param {React.ElementType} icon       - Lucide icon di header card
 * @param {string}            title      - Judul card
 * @param {number|string}     total      - Angka di badge total (opsional)
 * @param {string}            totalLabel - Label badge total, default 'Data'
 * @param {object}            pagination - Objek paginator Laravel (links, from, to, total)
 * @param {React.ReactNode}   children   - Konten tabel
 * @param {React.ReactNode}   headerExtra- Elemen tambahan di kanan header card (opsional, selain badge total)
 * @param {string}            className  - Override class container luar
 * @param {boolean}           noPadding  - True jika konten tidak perlu padding (tabel full-width)
 *
 * Kenapa ada noPadding?
 * Tabel pakai overflow-x-auto dan table full-width, sehingga container child tidak boleh punya padding.
 * Card-card non-tabel (seperti grid berita) butuh padding. Default: tidak ada padding (untuk tabel).
 */
export default function TableCard({
    icon: Icon,
    title,
    total,
    totalLabel = 'Data',
    pagination,
    children,
    headerExtra,
    className = '',
    noPadding = false,
}) {
    return (
        <div className={cn('table-card-wrapper bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden', className)}>
            {/* Header section */}
            <div className="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-gray-50/50 to-white shrink-0">
                <h3 className="text-base font-black text-gray-900 uppercase italic tracking-tighter flex items-center gap-3">
                    {Icon && <Icon className="w-5 h-5 text-green-600" />}
                    {title}
                </h3>
                <div className="flex items-center gap-2">
                    {headerExtra}
                    {total !== undefined && total !== null && (
                        <span className="px-4 py-1.5 bg-green-50 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 italic">
                            {typeof total === 'number' ? total.toLocaleString('id-ID') : total} {totalLabel}
                        </span>
                    )}
                </div>
            </div>

            {/* Konten */}
            <div className={noPadding ? '' : 'p-6'}>
                {children}
            </div>

            {/* Pagination footer — hanya muncul jika ada links dengan lebih dari 3 item */}
            {pagination?.links && pagination.links.length > 3 && (
                <div className="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                    <Pagination
                        links={pagination.links}
                        from={pagination.from}
                        to={pagination.to}
                        total={pagination.total}
                    />
                </div>
            )}
        </div>
    );
}

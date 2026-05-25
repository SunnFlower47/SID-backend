import React from 'react';
import { Link } from '@inertiajs/react';
import { Eye, Edit, Trash2 } from 'lucide-react';
import { cn } from '@/lib/utils';

/**
 * ActionButtons — Tombol aksi icon (View/Edit/Delete) di setiap baris tabel.
 *
 * @param {string}   viewHref   - Href tombol lihat detail (opsional, jika tidak ada = tidak ditampilkan)
 * @param {string}   editHref   - Href tombol edit (opsional)
 * @param {function} onDelete   - Handler hapus (opsional, jika tidak ada = tidak ditampilkan)
 * @param {Array}    extras     - Tombol tambahan: [{ icon: IconComponent, href?, onClick?, title?, className? }]
 * @param {string}   className  - Override wrapper class
 *
 * Kenapa pakai pola p-2 hover:bg-X/hover:text-white?
 * Dari audit halaman Keuangan/APBDes yang pakai gaya berbeda (p-2 text-gray-400 hover:text-color)
 * dibanding halaman lain yang pakai w-8 h-8 flex items-center. Komponen ini menyatukan ke pola
 * w-8 h-8 yang lebih banyak digunakan, tapi expose prop 'extras' untuk tombol custom seperti Keuangan.
 */
export default function ActionButtons({
    viewHref,
    editHref,
    onDelete,
    extras = [],
    className = '',
}) {
    return (
        <div className={cn('flex items-center justify-end gap-1.5', className)}>
            {/* Tombol extra di kiri (misalnya Histori, Cetak) */}
            {extras.map((extra, i) => {
                const ExtraIcon = extra.icon;
                const baseClass = cn(
                    'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
                    extra.className ?? 'bg-gray-50 text-gray-500 hover:bg-gray-800 hover:text-white'
                );

                return extra.href ? (
                    extra.isNative ? (
                        <a key={i} href={extra.href} target={extra.target} className={baseClass} title={extra.title}>
                            <ExtraIcon className="w-4 h-4" />
                        </a>
                    ) : (
                        <Link key={i} href={extra.href} className={baseClass} title={extra.title}>
                            <ExtraIcon className="w-4 h-4" />
                        </Link>
                    )
                ) : (
                    <button key={i} type="button" onClick={extra.onClick} className={baseClass} title={extra.title}>
                        <ExtraIcon className="w-4 h-4" />
                    </button>
                );
            })}

            {/* Tombol View */}
            {viewHref && (
                <Link
                    href={viewHref}
                    className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                    title="Lihat Detail"
                >
                    <Eye className="w-4 h-4" />
                </Link>
            )}

            {/* Tombol Edit */}
            {editHref && (
                <Link
                    href={editHref}
                    className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors"
                    title="Edit"
                >
                    <Edit className="w-4 h-4" />
                </Link>
            )}

            {/* Tombol Delete */}
            {onDelete && (
                <button
                    type="button"
                    onClick={onDelete}
                    className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                    title="Hapus"
                >
                    <Trash2 className="w-4 h-4" />
                </button>
            )}
        </div>
    );
}

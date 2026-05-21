import React from 'react';
import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

/**
 * EmptyState — State kosong dengan animasi Lottie, pesan, dan CTA opsional.
 *
 * @param {string}  title     - Judul state kosong (wajib)
 * @param {string}  message   - Deskripsi tambahan (opsional)
 * @param {object}  action    - Tombol CTA: { label, href?, onClick?, icon?: IconComponent }
 * @param {string}  size      - Ukuran animasi: 'sm' | 'md' | 'lg' (default: 'md')
 * @param {string}  className - Override class container
 *
 * Ukuran Lottie berdasarkan konteks:
 * - 'sm': w-40 h-40 — untuk empty state di dalam tabel (mobile card)
 * - 'md': w-48 h-48 — untuk empty state desktop default (paling umum)
 * - 'lg': w-72 h-72 — untuk empty state full-page seperti di Berita/Index
 */
export default function EmptyState({
    title = 'Belum Ada Data',
    message,
    action,
    size = 'md',
    className = '',
}) {
    const sizeMap = {
        sm: 'w-40 h-40',
        md: 'w-48 h-48',
        lg: 'w-72 h-72',
    };

    const ActionIcon = action?.icon;

    return (
        <div className={cn('flex flex-col items-center justify-center text-center py-12', className)}>
            <div className={cn('mx-auto mb-2', sizeMap[size] ?? sizeMap.md)}>
                <LottieComponent animationData={noDataAnimation} loop={true} />
            </div>

            <p className="text-sm font-black text-gray-900 uppercase italic tracking-tighter mt-2">
                {title}
            </p>

            {message && (
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2 max-w-xs">
                    {message}
                </p>
            )}

            {action && (
                action.href ? (
                    <Link
                        href={action.href}
                        className="inline-flex items-center mt-6 px-8 py-3.5 bg-green-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-green-200 hover:bg-green-700 transition-all hover:scale-105"
                    >
                        {ActionIcon && <ActionIcon className="w-4 h-4 mr-2" />}
                        {action.label}
                    </Link>
                ) : (
                    <button
                        type="button"
                        onClick={action.onClick}
                        className="inline-flex items-center mt-6 px-8 py-3.5 bg-green-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-green-200 hover:bg-green-700 transition-all hover:scale-105"
                    >
                        {ActionIcon && <ActionIcon className="w-4 h-4 mr-2" />}
                        {action.label}
                    </button>
                )
            )}
        </div>
    );
}

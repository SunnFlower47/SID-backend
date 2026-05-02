import React from 'react';
import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export default function Pagination({ links, from, to, total }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Menampilkan {from || 0} - {to || 0} dari {total || 0} data
            </p>
            <div className="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0 max-w-full">
                {links.map((link, i) => (
                    <Link
                        key={i}
                        href={link.url || '#'}
                        className={cn(
                            "px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap",
                            link.active ? 'bg-green-600 text-white shadow-md' : 
                            link.url ? 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-100' : 
                            'bg-transparent text-gray-300 cursor-not-allowed'
                        )}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                        preserveScroll
                        preserveState
                    />
                ))}
            </div>
        </div>
    );
}

import React, { useState } from 'react';
import { Search, Filter } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function FilterContainer({ 
    hasActiveFilters = false, 
    children, 
    title = "Konfigurasi Data",
    subtitle = "Pencarian & Filter"
}) {
    const [showFilters, setShowFilters] = useState(hasActiveFilters);

    return (
        <div className="mb-6 space-y-4">
            <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                <div className="flex items-center gap-2 sm:gap-4">
                    <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">{title}</h3>
                        <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">{subtitle}</p>
                    </div>
                </div>
                <button
                    type="button"
                    onClick={() => setShowFilters(!showFilters)}
                    className={cn(
                        "flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95",
                        showFilters
                            ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                            : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                    )}
                >
                    <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                    {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                </button>
            </div>

            {showFilters && (
                <div className="animate-in slide-in-from-top-2 duration-300">
                    {children}
                </div>
            )}
        </div>
    );
}

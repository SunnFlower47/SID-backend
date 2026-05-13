import React from 'react';
import { cn } from '@/lib/utils';

export default function AnggaranProgressBar({ anggaran = 0, realisasi = 0, showLabels = true, height = 'h-2' }) {
    const pct = anggaran > 0 ? Math.min(100, Math.round((realisasi / anggaran) * 100)) : 0;
    const sisa = anggaran - realisasi;

    const barColor =
        pct >= 90 ? 'bg-green-500' :
        pct >= 60 ? 'bg-blue-500'  :
        pct >= 30 ? 'bg-yellow-400' :
                    'bg-gray-300';

    const textColor =
        pct >= 90 ? 'text-green-600' :
        pct >= 60 ? 'text-blue-600'  :
        pct >= 30 ? 'text-yellow-600' :
                    'text-gray-400';

    const formatRupiah = (v) => {
        if (!v && v !== 0) return '0';
        if (Math.abs(v) >= 1_000_000_000) return `${(v / 1_000_000_000).toFixed(1)} M`;
        if (Math.abs(v) >= 1_000_000) return `${(v / 1_000_000).toFixed(1)} Jt`;
        return v.toLocaleString('id-ID');
    };

    return (
        <div className="space-y-1.5">
            {showLabels && (
                <div className="flex items-center justify-between">
                    <span className={cn('text-[9px] font-black uppercase tracking-widest', textColor)}>{pct}% Terserap</span>
                    <span className="text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                        Sisa: Rp {formatRupiah(sisa)}
                    </span>
                </div>
            )}
            <div className={cn('w-full bg-gray-100 rounded-full overflow-hidden', height)}>
                <div
                    className={cn('h-full rounded-full transition-all duration-700', barColor)}
                    style={{ width: `${pct}%` }}
                />
            </div>
            {showLabels && (
                <div className="flex items-center justify-between">
                    <span className="text-[9px] font-bold text-gray-400">
                        Rp {formatRupiah(realisasi)} / Rp {formatRupiah(anggaran)}
                    </span>
                </div>
            )}
        </div>
    );
}

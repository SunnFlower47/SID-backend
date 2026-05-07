import React from 'react';
import { MessageSquare, CheckCircle, Clock, Star } from 'lucide-react';

const STAT_CONFIG = [
    {
        key: 'total',
        label: 'Total Testimoni',
        icon: MessageSquare,
        iconBg: 'bg-indigo-100',
        iconColor: 'text-indigo-600',
        border: 'border-indigo-100',
    },
    {
        key: 'approved',
        label: 'Disetujui',
        icon: CheckCircle,
        iconBg: 'bg-emerald-100',
        iconColor: 'text-emerald-600',
        border: 'border-emerald-100',
    },
    {
        key: 'pending',
        label: 'Menunggu',
        icon: Clock,
        iconBg: 'bg-amber-100',
        iconColor: 'text-amber-600',
        border: 'border-amber-100',
    },
    {
        key: 'avg_rating',
        label: 'Avg Rating',
        icon: Star,
        iconBg: 'bg-orange-100',
        iconColor: 'text-orange-600',
        border: 'border-orange-100',
    },
];

export default function TestimoniStats({ stats = {} }) {
    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {STAT_CONFIG.map(({ key, label, icon: Icon, iconBg, iconColor, border }) => (
                <div
                    key={key}
                    className={`bg-white border ${border} rounded-2xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4 shadow-sm hover:shadow-md transition-all text-left`}
                >
                    <div className={`${iconBg} w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0`}>
                        <Icon className={`w-4 h-4 sm:w-5 sm:h-5 ${iconColor}`} />
                    </div>
                    <div className="min-w-0">
                        <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">
                            {label}
                        </p>
                        <h3 className="text-xl sm:text-2xl font-black text-gray-900 leading-none flex items-baseline gap-1">
                            {key === 'avg_rating' ? parseFloat(stats[key] || 0).toFixed(1) : (stats[key] || 0).toLocaleString('id-ID')}
                            {key === 'avg_rating' && <span className="text-[10px] font-bold text-gray-400">/5</span>}
                        </h3>
                    </div>
                </div>
            ))}
        </div>
    );
}

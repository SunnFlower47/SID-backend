import React from 'react';
import { MessageSquare, AlertCircle, Clock, CheckCircle } from 'lucide-react';

const STAT_CONFIG = [
    {
        key: 'total',
        label: 'Total Aduan',
        icon: MessageSquare,
        bg: 'bg-blue-50',
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600',
        textColor: 'text-blue-600',
        border: 'border-blue-200',
    },
    {
        key: 'baru',
        label: 'Aduan Baru',
        icon: AlertCircle,
        bg: 'bg-yellow-50',
        iconBg: 'bg-yellow-100',
        iconColor: 'text-yellow-600',
        textColor: 'text-yellow-600',
        border: 'border-yellow-200',
    },
    {
        key: 'diproses',
        label: 'Sedang Diproses',
        icon: Clock,
        bg: 'bg-purple-50',
        iconBg: 'bg-purple-100',
        iconColor: 'text-purple-600',
        textColor: 'text-purple-600',
        border: 'border-purple-200',
    },
    {
        key: 'darurat',
        label: 'Prioritas Darurat',
        icon: AlertCircle, // using AlertCircle again or maybe something like TriangleAlert if available, but AlertCircle is fine
        bg: 'bg-red-50',
        iconBg: 'bg-red-100',
        iconColor: 'text-red-600',
        textColor: 'text-red-600',
        border: 'border-red-200',
    },
];

export default function PengaduanStats({ stats = {} }) {
    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {STAT_CONFIG.map(({ key, label, icon: Icon, bg, iconBg, iconColor, textColor, border }) => (
                <div
                    key={key}
                    className={`bg-white border ${border} rounded-2xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4 shadow-sm hover:shadow-md transition-shadow`}
                >
                    <div className={`${iconBg} w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0`}>
                        <Icon className={`w-4 h-4 sm:w-5 sm:h-5 ${iconColor}`} />
                    </div>
                    <div className="min-w-0">
                        <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">
                            {label}
                        </p>
                        <p className="text-xl sm:text-2xl font-black text-gray-900 leading-none">
                            {(stats[key] ?? 0).toLocaleString('id-ID')}
                        </p>
                    </div>
                </div>
            ))}
        </div>
    );
}

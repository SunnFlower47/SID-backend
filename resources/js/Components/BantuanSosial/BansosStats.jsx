import React from 'react';
import { List, CheckCircle, Users, UserCheck } from 'lucide-react';

const STAT_CONFIG = [
    {
        key: 'total_program',
        label: 'Total Program',
        icon: List,
        color: 'blue',
        bg: 'bg-blue-50',
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600',
        textColor: 'text-blue-600',
        border: 'border-blue-200',
    },
    {
        key: 'program_aktif',
        label: 'Program Aktif',
        icon: CheckCircle,
        color: 'green',
        bg: 'bg-green-50',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
        textColor: 'text-green-600',
        border: 'border-green-200',
    },
    {
        key: 'total_penerima',
        label: 'Total Penerima',
        icon: Users,
        color: 'purple',
        bg: 'bg-purple-50',
        iconBg: 'bg-purple-100',
        iconColor: 'text-purple-600',
        textColor: 'text-purple-600',
        border: 'border-purple-200',
    },
    {
        key: 'penerima_aktif',
        label: 'Penerima Aktif',
        icon: UserCheck,
        color: 'yellow',
        bg: 'bg-yellow-50',
        iconBg: 'bg-yellow-100',
        iconColor: 'text-yellow-600',
        textColor: 'text-yellow-600',
        border: 'border-yellow-200',
    },
];

export default function BansosStats({ stats = {} }) {
    return (
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {STAT_CONFIG.map(({ key, label, icon: Icon, bg, iconBg, iconColor, textColor, border }) => (
                <div
                    key={key}
                    className={`${bg} border ${border} rounded-2xl p-4 sm:p-5 flex items-center gap-3 sm:gap-4 hover:shadow-md transition-shadow`}
                >
                    <div className={`${iconBg} w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0`}>
                        <Icon className={`w-5 h-5 sm:w-6 sm:h-6 ${iconColor}`} />
                    </div>
                    <div className="min-w-0">
                        <p className={`text-[10px] sm:text-xs font-black ${textColor} uppercase tracking-widest truncate`}>
                            {label}
                        </p>
                        <p className="text-2xl sm:text-3xl font-black text-gray-900 leading-none mt-0.5">
                            {(stats[key] ?? 0).toLocaleString('id-ID')}
                        </p>
                    </div>
                </div>
            ))}
        </div>
    );
}

import React from 'react';
import { Baby, UserX, MapPin, Split } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function MutasiStats({ stats = {} }) {
    const statCards = [
        { label: 'Kelahiran', value: stats?.kelahiran || 0, color: 'blue', icon: Baby },
        { label: 'Kematian', value: stats?.kematian || 0, color: 'red', icon: UserX },
        { label: 'Pindahan', value: stats?.pindahan || 0, color: 'green', icon: MapPin },
        { label: 'Pisah KK', value: stats?.pisah_kk || 0, color: 'purple', icon: Split },
    ];

    const colorClasses = {
        blue: 'border-blue-100 bg-blue-50 text-blue-600 shadow-blue-100/50',
        red: 'border-red-100 bg-red-50 text-red-600 shadow-red-100/50',
        green: 'border-green-100 bg-green-50 text-green-600 shadow-green-100/50',
        purple: 'border-purple-100 bg-purple-50 text-purple-600 shadow-purple-100/50',
    };

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {statCards.map((item, idx) => {
                const Icon = item.icon;
                const colors = colorClasses[item.color] || colorClasses.blue;

                return (
                    <div
                        key={idx}
                        className={cn(
                            "bg-white rounded-2xl p-3 sm:p-4 border shadow-sm hover:shadow-md transition-all flex items-center gap-3 sm:gap-4",
                            colors.split(' ')[0], // border class
                            colors.split(' ')[3]  // shadow class
                        )}
                    >
                        <div className={cn(
                            "w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110",
                            colors.split(' ')[1], // bg class
                            colors.split(' ')[2]  // text class
                        )}>
                            <Icon className="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">
                                {item.label}
                            </p>
                            <h4 className="text-lg sm:text-2xl font-black text-gray-900 leading-none">
                                {item.value?.toLocaleString('id-ID')}
                            </h4>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

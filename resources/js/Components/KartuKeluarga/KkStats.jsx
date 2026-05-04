import React from 'react';
import { Home, CheckCircle, Ban, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function KkStats({ stats }) {
    const statCards = [
        {
            title: 'Total KK',
            value: stats.total,
            icon: Home,
            color: 'blue'
        },
        {
            title: 'KK Aktif',
            value: stats.aktif,
            icon: CheckCircle,
            color: 'green'
        },
        {
            title: 'KK Kosong',
            value: stats.kosong,
            icon: Ban,
            color: 'orange'
        },
        {
            title: 'Bermasalah',
            value: stats.bermasalah,
            icon: AlertTriangle,
            color: 'red'
        }
    ];

    const colorClasses = {
        blue: 'border-blue-100 bg-blue-50 text-blue-600 shadow-blue-100/50',
        green: 'border-green-100 bg-green-50 text-green-600 shadow-green-100/50',
        orange: 'border-orange-100 bg-orange-50 text-orange-600 shadow-orange-100/50',
        red: 'border-red-100 bg-red-50 text-red-600 shadow-red-100/50',
    };

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {statCards.map((stat, index) => {
                const Icon = stat.icon;
                const colors = colorClasses[stat.color] || colorClasses.blue;
                
                return (
                    <div 
                        key={index} 
                        className={cn(
                            "bg-white rounded-2xl p-4 sm:p-6 border shadow-sm hover:shadow-md transition-all flex items-center gap-3 sm:gap-4",
                            colors.split(' ')[0], // border class
                            colors.split(' ')[3]  // shadow class
                        )}
                    >
                        <div className={cn(
                            "w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0",
                            colors.split(' ')[1], // bg class
                            colors.split(' ')[2]  // text class
                        )}>
                            <Icon className="w-5 h-5 sm:w-6 sm:h-6" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">
                                {stat.title}
                            </p>
                            <h3 className="text-lg sm:text-2xl font-black text-gray-900 leading-none">
                                {stat.value?.toLocaleString('id-ID')}
                            </h3>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

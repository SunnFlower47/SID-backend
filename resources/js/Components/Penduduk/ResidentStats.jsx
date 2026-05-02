import React from 'react';
import { Users, User, UserCheck, Home } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function ResidentStats({ stats }) {
    const statCards = [
        {
            title: 'Total Penduduk',
            value: stats.total,
            icon: Users,
            color: 'blue'
        },
        {
            title: 'Laki-Laki',
            value: stats.laki_laki,
            icon: User,
            color: 'cyan'
        },
        {
            title: 'Perempuan',
            value: stats.perempuan,
            icon: UserCheck,
            color: 'pink'
        },
        {
            title: 'Total KK',
            value: stats.total_kk,
            icon: Home,
            color: 'green'
        }
    ];

    const colorClasses = {
        blue: 'border-blue-100 bg-blue-50 text-blue-600 shadow-blue-100/50',
        pink: 'border-pink-100 bg-pink-50 text-pink-600 shadow-pink-100/50',
        green: 'border-green-100 bg-green-50 text-green-600 shadow-green-100/50',
        orange: 'border-orange-100 bg-orange-50 text-orange-600 shadow-orange-100/50',
    };

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {statCards.map((stat, index) => {
                const Icon = stat.icon;
                const colors = colorClasses[stat.color] || colorClasses.green;

                return (
                    <div
                        key={index}
                        className={cn(
                            "bg-white rounded-2xl p-2.5 sm:p-5 border shadow-sm hover:shadow-md transition-all flex items-center gap-2.5 sm:gap-4",
                            colors.split(' ')[0], // border class
                            colors.split(' ')[3]  // shadow class
                        )}
                    >
                        <div className={cn(
                            "w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center shrink-0",
                            colors.split(' ')[1], // bg class
                            colors.split(' ')[2]  // text class
                        )}>
                            <Icon className="w-4 h-4 sm:w-6 sm:h-6" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-[8px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none">
                                {stat.title === 'Total Penduduk' ? (
                                    <>
                                        <span className="hidden sm:inline">Total Penduduk</span>
                                        <span className="inline sm:hidden">Warga</span>
                                    </>
                                ) : stat.title}
                            </p>
                            <h3 className="text-base sm:text-2xl font-black text-gray-900 leading-none mt-1">{stat.value?.toLocaleString('id-ID')}</h3>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

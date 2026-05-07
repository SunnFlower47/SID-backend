import React from 'react';
import { Mailbox, MailWarning, MailCheck, Archive, Mail } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function ContactMessageStats({ stats = {} }) {
    const statCards = [
        {
            title: 'Total Pesan',
            value: stats.total,
            icon: Mailbox,
            color: 'blue'
        },
        {
            title: 'Belum Dibaca',
            value: stats.unread,
            icon: MailWarning,
            color: 'red'
        },
        {
            title: 'Sudah Dijawab',
            value: stats.replied,
            icon: MailCheck,
            color: 'emerald'
        },
        {
            title: 'Diarsipkan',
            value: stats.archived,
            icon: Archive,
            color: 'gray'
        }
    ];

    const colorClasses = {
        blue: 'border-blue-100 bg-blue-50 text-blue-600 shadow-blue-100/50',
        red: 'border-red-100 bg-red-50 text-red-600 shadow-red-100/50',
        emerald: 'border-emerald-100 bg-emerald-50 text-emerald-600 shadow-emerald-100/50',
        gray: 'border-gray-100 bg-gray-50 text-gray-600 shadow-gray-100/50',
    };

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {statCards.map((stat, index) => {
                const Icon = stat.icon;
                const colors = colorClasses[stat.color] || colorClasses.blue;

                return (
                    <div
                        key={index}
                        className={cn(
                            "bg-white rounded-2xl p-3 sm:p-4 border shadow-sm hover:shadow-md transition-all flex items-center gap-3 sm:gap-4",
                            colors.split(' ')[0], // border class
                            colors.split(' ')[3]  // shadow class
                        )}
                    >
                        <div className={cn(
                            "w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0",
                            colors.split(' ')[1], // bg class
                            colors.split(' ')[2]  // text class
                        )}>
                            <Icon className="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1 text-left">
                                {stat.title}
                            </p>
                            <h3 className="text-xl sm:text-2xl font-black text-gray-900 leading-none text-left">
                                {stat.value?.toLocaleString('id-ID') ?? 0}
                            </h3>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

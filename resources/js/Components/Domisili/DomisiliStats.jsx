import React from 'react';
import { CheckCircle, Clock, UserCheck, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function DomisiliStats({ stats }) {
    const cards = [
        { title: 'Domisili Aktif', value: stats?.total_aktif, icon: CheckCircle, color: 'green' },
        { title: 'Expired Bulan Ini', value: stats?.expired_bulan_ini, icon: Clock, color: 'orange' },
        { title: 'Baru Masuk Bulan Ini', value: stats?.baru_masuk_bulan_ini, icon: UserCheck, color: 'blue' },
        { title: 'Akan Expired (30h)', value: stats?.warning_expired, icon: AlertTriangle, color: 'red' },
    ];
    
    const colors = {
        green: 'border-green-100 bg-green-50 text-green-600 shadow-green-100/50',
        orange: 'border-orange-100 bg-orange-50 text-orange-600 shadow-orange-100/50',
        blue: 'border-blue-100 bg-blue-50 text-blue-600 shadow-blue-100/50',
        red: 'border-red-100 bg-red-50 text-red-600 shadow-red-100/50',
    };

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {cards.map((c, i) => {
                const Icon = c.icon;
                const [border, bg, text, shadow] = colors[c.color].split(' ');
                return (
                    <div key={i} className={cn('bg-white rounded-2xl p-3 sm:p-4 border shadow-sm hover:shadow-md transition-all flex items-center gap-3 sm:gap-4', border, shadow)}>
                        <div className={cn('w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0', bg, text)}>
                            <Icon className="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest truncate leading-none mb-1">{c.title}</p>
                            <h3 className="text-xl sm:text-2xl font-black text-gray-900 leading-none">{c.value?.toLocaleString('id-ID') ?? 0}</h3>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

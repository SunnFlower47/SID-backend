import React from 'react';
import { CheckCircle, Clock, UserCheck, AlertTriangle } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function DomisiliStats({ stats }) {
    const cards = [
        { title: 'Domisili Aktif', value: stats?.total_aktif, icon: CheckCircle, color: 'green' },
        { title: 'Expired Bulan Ini', value: stats?.expired_bulan_ini, icon: Clock, color: 'orange' },
        { title: 'Baru Masuk Bulan Ini', value: stats?.baru_masuk_bulan_ini, icon: UserCheck, color: 'blue' },
        { title: 'Akan Expired (30h)', value: stats?.warning_expired, icon: AlertTriangle, color: 'rose' },
    ];

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {cards.map((c, i) => (
                <StatCard
                    key={i}
                    title={c.title}
                    value={c.value}
                    icon={c.icon}
                    color={c.color}
                />
            ))}
        </div>
    );
}

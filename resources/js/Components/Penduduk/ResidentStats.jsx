import React from 'react';
import { Users, User, UserCheck, Home } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function ResidentStats({ stats }) {
    const statCards = [
        { label: 'Total Penduduk', value: stats.total, icon: Users, color: 'blue' },
        { label: 'Laki-Laki', value: stats.laki_laki, icon: User, color: 'teal' },
        { label: 'Perempuan', value: stats.perempuan, icon: UserCheck, color: 'rose' },
        { label: 'Total KK', value: stats.total_kk, icon: Home, color: 'green' }
    ];

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {statCards.map((stat, index) => (
                <StatCard 
                    key={index}
                    icon={stat.icon}
                    label={stat.label}
                    value={stat.value?.toLocaleString('id-ID')}
                    color={stat.color}
                />
            ))}
        </div>
    );
}

import React from 'react';
import { Home, CheckCircle, Ban, AlertTriangle } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function KkStats({ stats }) {
    const statCards = [
        { label: 'Total KK', value: stats.total, icon: Home, color: 'blue' },
        { label: 'KK Aktif', value: stats.aktif, icon: CheckCircle, color: 'green' },
        { label: 'KK Kosong', value: stats.kosong, icon: Ban, color: 'orange' },
        { label: 'Bermasalah', value: stats.bermasalah, icon: AlertTriangle, color: 'rose' }
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

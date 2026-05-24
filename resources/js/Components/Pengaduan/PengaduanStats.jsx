import React from 'react';
import { MessageSquare, AlertCircle, Clock, CheckCircle } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function PengaduanStats({ stats = {} }) {
    const statCards = [
        { key: 'total', label: 'Total Aduan', icon: MessageSquare, color: 'blue' },
        { key: 'baru', label: 'Aduan Baru', icon: AlertCircle, color: 'yellow' },
        { key: 'diproses', label: 'Sedang Diproses', icon: Clock, color: 'purple' },
        { key: 'darurat', label: 'Prioritas Darurat', icon: AlertCircle, color: 'rose' }
    ];

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {statCards.map((stat) => (
                <StatCard
                    key={stat.key}
                    label={stat.label}
                    value={stats[stat.key]}
                    icon={stat.icon}
                    color={stat.color}
                />
            ))}
        </div>
    );
}

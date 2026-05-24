import React from 'react';
import { List, CheckCircle, Users, UserCheck } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function BansosStats({ stats = {} }) {
    const statCards = [
        { key: 'total_program', label: 'Total Program', icon: List, color: 'blue' },
        { key: 'program_aktif', label: 'Program Aktif', icon: CheckCircle, color: 'green' },
        { key: 'total_penerima', label: 'Total Penerima', icon: Users, color: 'purple' },
        { key: 'penerima_aktif', label: 'Penerima Aktif', icon: UserCheck, color: 'yellow' }
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

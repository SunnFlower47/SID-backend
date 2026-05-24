import React from 'react';
import { Baby, UserX, MapPin, Split } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function MutasiStats({ stats = {} }) {
    const statCards = [
        { label: 'Kelahiran', value: stats?.kelahiran || 0, color: 'blue', icon: Baby },
        { label: 'Kematian', value: stats?.kematian || 0, color: 'rose', icon: UserX },
        { label: 'Pindahan', value: stats?.pindahan || 0, color: 'green', icon: MapPin },
        { label: 'Pisah KK', value: stats?.pisah_kk || 0, color: 'purple', icon: Split },
    ];

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            {statCards.map((stat, idx) => (
                <StatCard
                    key={idx}
                    label={stat.label}
                    value={stat.value}
                    icon={stat.icon}
                    color={stat.color}
                />
            ))}
        </div>
    );
}

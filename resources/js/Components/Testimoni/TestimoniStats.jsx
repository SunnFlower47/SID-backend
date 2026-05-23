import React from 'react';
import { MessageSquare, CheckCircle, Clock, Star } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

const STAT_CONFIG = [
    {
        key: 'total',
        label: 'Total Testimoni',
        icon: MessageSquare,
        color: 'indigo'
    },
    {
        key: 'approved',
        label: 'Disetujui',
        icon: CheckCircle,
        color: 'emerald'
    },
    {
        key: 'pending',
        label: 'Menunggu',
        icon: Clock,
        color: 'amber'
    },
    {
        key: 'avg_rating',
        label: 'Avg Rating',
        icon: Star,
        color: 'orange'
    },
];

export default function TestimoniStats({ stats = {} }) {
    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-left">
            {STAT_CONFIG.map(({ key, label, icon: Icon, color }) => (
                <StatCard
                    key={key}
                    title={label}
                    value={
                        key === 'avg_rating' 
                            ? `${parseFloat(stats[key] || 0).toFixed(1)}/5` 
                            : (stats[key] || 0).toLocaleString('id-ID')
                    }
                    icon={Icon}
                    color={color}
                />
            ))}
        </div>
    );
}

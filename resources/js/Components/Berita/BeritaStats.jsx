import React from 'react';
import { Newspaper, CheckCircle, Clock, Tags } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function BeritaStats({ stats = {} }) {
    const statCards = [
        {
            title: 'Total Berita',
            value: stats.total,
            icon: Newspaper,
            color: 'blue'
        },
        {
            title: 'Diterbitkan',
            value: stats.published,
            icon: CheckCircle,
            color: 'emerald'
        },
        {
            title: 'Draft (Arsip)',
            value: stats.draft,
            icon: Clock,
            color: 'orange'
        },
        {
            title: 'Kategori',
            value: stats.categories_count,
            icon: Tags,
            color: 'purple'
        }
    ];

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-left">
            {statCards.map((stat, index) => (
                <StatCard
                    key={index}
                    title={stat.title}
                    value={stat.value?.toLocaleString('id-ID') ?? 0}
                    icon={stat.icon}
                    color={stat.color}
                />
            ))}
        </div>
    );
}

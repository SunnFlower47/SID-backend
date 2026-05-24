import React from 'react';
import { Store, CheckCircle, Star, ShieldCheck } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function UmkmStats({ stats = {} }) {
    const statCards = [
        { title: 'Total UMKM', value: stats.total, icon: Store, color: 'blue' },
        { title: 'UMKM Aktif', value: stats.aktif, icon: CheckCircle, color: 'green' },
        { title: 'Unggulan', value: stats.unggulan, icon: Star, color: 'orange' },
        { title: 'Terverifikasi', value: stats.verified, icon: ShieldCheck, color: 'purple' }
    ];

    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-left">
            {statCards.map((stat, index) => (
                <StatCard
                    key={index}
                    title={stat.title}
                    value={stat.value}
                    icon={stat.icon}
                    color={stat.color}
                />
            ))}
        </div>
    );
}

import React from 'react';
import { Building2, CheckCircle, GraduationCap, HeartPulse } from 'lucide-react';
import { StatCard } from '@/Components/Shared';

export default function FasilitasDesaStats({ stats = {} }) {
    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <StatCard
                icon={Building2}
                label="Total Fasilitas"
                value={stats.total}
                color="blue"
            />
            <StatCard
                icon={CheckCircle}
                label="Fasilitas Aktif"
                value={stats.aktif}
                color="emerald"
            />
            <StatCard
                icon={GraduationCap}
                label="Pendidikan"
                value={stats.pendidikan}
                color="purple"
            />
            <StatCard
                icon={HeartPulse}
                label="Kesehatan"
                value={stats.kesehatan}
                color="orange"
            />
        </div>
    );
}

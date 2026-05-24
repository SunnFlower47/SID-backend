import React from 'react';
import { PieChart, Pie, Cell, Tooltip, ResponsiveContainer } from 'recharts';

const STATUS_CONFIG = {
    perencanaan: { color: '#3b82f6', label: 'Perencanaan' },
    pelaksanaan:  { color: '#f59e0b', label: 'Pelaksanaan' },
    selesai:      { color: '#10b981', label: 'Selesai'     },
    tertunda:     { color: '#6b7280', label: 'Tertunda'    },
    dibatalkan:   { color: '#ef4444', label: 'Dibatalkan'  },
};

const CustomTooltip = ({ active, payload }) => {
    if (!active || !payload?.length) return null;
    const { name, value } = payload[0];
    return (
        <div className="bg-white rounded-2xl shadow-xl border border-gray-100 p-3 text-left">
            <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">{name}</p>
            <p className="text-base font-black text-gray-900">{value} proyek</p>
        </div>
    );
};

export default function ProyekDonutChart({ data = [] }) {
    const chartData = data.map((d) => ({
        name:  STATUS_CONFIG[d.status]?.label ?? d.status,
        value: d.total,
        color: STATUS_CONFIG[d.status]?.color ?? '#9ca3af',
    }));

    const total = chartData.reduce((sum, d) => sum + d.value, 0);

    if (total === 0) {
        return (
            <div className="flex flex-col items-center justify-center h-full text-gray-300 py-10">
                <p className="text-[10px] font-black uppercase tracking-widest">Belum ada proyek</p>
            </div>
        );
    }

    return (
        <div className="w-full flex flex-col">
            <div style={{ height: '180px' }} className="relative">
                <ResponsiveContainer width="100%" height="100%" minWidth={1} minHeight={1}>
                    <PieChart>
                        <Pie
                            data={chartData}
                            cx="50%"
                            cy="50%"
                            innerRadius={50}
                            outerRadius={75}
                            paddingAngle={3}
                            dataKey="value"
                            strokeWidth={0}
                        >
                            {chartData.map((entry, index) => (
                                <Cell key={index} fill={entry.color} />
                            ))}
                        </Pie>
                        <Tooltip content={<CustomTooltip />} />
                    </PieChart>
                </ResponsiveContainer>
                {/* Center label */}
                <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p className="text-2xl font-black text-gray-900 leading-none">{total}</p>
                    <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Proyek</p>
                </div>
            </div>
            <div className="flex flex-col gap-1.5 mt-4 px-1">
                {chartData.map((item) => (
                    <div key={item.name} className="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100/50">
                        <div className="flex items-center gap-2">
                            <div className="w-2.5 h-2.5 rounded-full" style={{ backgroundColor: item.color }} />
                            <span className="text-[10px] font-black text-gray-700 uppercase tracking-tight">{item.name}</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className="text-xs font-black text-gray-900">{item.value}</span>
                            <span className="text-[9px] font-bold text-gray-400">
                                ({total > 0 ? Math.round((item.value / total) * 100) : 0}%)
                            </span>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

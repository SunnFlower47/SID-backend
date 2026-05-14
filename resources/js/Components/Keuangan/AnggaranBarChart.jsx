import React from 'react';
import {
    BarChart, Bar, XAxis, YAxis, CartesianGrid,
    Tooltip, ResponsiveContainer, Legend
} from 'recharts';

const formatRupiahAxis = (value) => {
    if (value >= 1_000_000_000) return `${(value / 1_000_000_000).toFixed(1)}M`;
    if (value >= 1_000_000) return `${(value / 1_000_000).toFixed(0)}Jt`;
    if (value >= 1_000) return `${(value / 1_000).toFixed(0)}Rb`;
    return value;
};

const CustomTooltip = ({ active, payload, label }) => {
    if (!active || !payload?.length) return null;
    return (
        <div className="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 text-left min-w-[160px]">
            <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">{label}</p>
            {payload.map((p, i) => (
                <div key={i} className="flex items-center gap-2 mb-1">
                    <div className="w-2 h-2 rounded-full" style={{ backgroundColor: p.fill }} />
                    <span className="text-[10px] font-black text-gray-700 uppercase tracking-wider">{p.name}:</span>
                    <span className="text-[10px] font-black text-gray-900">
                        Rp {Number(p.value).toLocaleString('id-ID')}
                    </span>
                </div>
            ))}
        </div>
    );
};

export default function AnggaranBarChart({ data = [] }) {
    // Ensure all 3 jenis are present even if DB has no records for them
    const jenisOrder = ['pendapatan', 'belanja', 'pembiayaan'];
    const chartData = jenisOrder.map((jenis) => {
        const found = data.find((d) => d.jenis === jenis);
        return {
            label: jenis.charAt(0).toUpperCase() + jenis.slice(1),
            Anggaran: found?.total_anggaran ?? 0,
            Realisasi: found?.total_realisasi ?? 0,
        };
    });

    return (
        <div className="w-full h-full flex flex-col">
            <div style={{ height: '220px' }}>
                <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={chartData} barCategoryGap="30%" barGap={4}>
                        <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                        <XAxis
                            dataKey="label"
                            axisLine={false}
                            tickLine={false}
                            tick={{ fontSize: 9, fontWeight: 800, fill: '#6b7280', textTransform: 'uppercase', letterSpacing: '0.1em' }}
                            dy={8}
                        />
                        <YAxis
                            axisLine={false}
                            tickLine={false}
                            tickFormatter={formatRupiahAxis}
                            tick={{ fontSize: 9, fontWeight: 800, fill: '#9ca3af' }}
                            width={45}
                        />
                        <Tooltip content={<CustomTooltip />} cursor={{ fill: '#f9fafb', radius: 8 }} />
                        <Bar dataKey="Anggaran"  fill="#10b981" radius={[6, 6, 0, 0]} barSize={28} />
                        <Bar dataKey="Realisasi" fill="#3b82f6" radius={[6, 6, 0, 0]} barSize={28} />
                    </BarChart>
                </ResponsiveContainer>
            </div>
            <div className="flex gap-5 mt-3 px-1">
                {[
                    { label: 'Anggaran',  color: '#10b981' },
                    { label: 'Realisasi', color: '#3b82f6' },
                ].map((item) => (
                    <div key={item.label} className="flex items-center gap-2">
                        <div className="w-2.5 h-2.5 rounded-full" style={{ backgroundColor: item.color }} />
                        <span className="text-[9px] font-black text-gray-500 uppercase tracking-widest">{item.label}</span>
                    </div>
                ))}
            </div>
        </div>
    );
}

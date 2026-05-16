import React from 'react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Cell, Legend } from 'recharts';

const COLORS = {
    'kelahiran': '#10b981',
    'kematian': '#ef4444',
    'pindah_masuk': '#3b82f6',
    'pindah_keluar': '#f59e0b',
    'pisah_kk': '#8b5cf6',
};

export default function MutasiBarChart({ data }) {
    // Format data from Laravel mutation stats
    const safeData = data ?? [];
    let chartData = [];

    if (Array.isArray(safeData)) {
        chartData = safeData.map(item => ({
            key: item.jenis_mutasi,
            name: (item.jenis_mutasi || '').replace(/_/g, ' ').toUpperCase(),
            total: item.total
        }));
    } else {
        chartData = Object.entries(safeData).map(([key, value]) => ({
            key,
            name: key.replace(/_/g, ' ').toUpperCase(),
            total: value
        }));
    }

    return (
        <div className="h-[300px] w-full">
            <ResponsiveContainer width="100%" height="100%">
                <BarChart data={chartData} margin={{ top: 20, right: 30, left: 0, bottom: 0 }}>
                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                    <XAxis 
                        dataKey="name" 
                        axisLine={false} 
                        tickLine={false} 
                        tick={{ fontSize: 8, fontWeight: '900', fill: '#9ca3af' }}
                    />
                    <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 9, fill: '#9ca3af' }} />
                    <Tooltip 
                        cursor={{ fill: '#f9fafb' }}
                        contentStyle={{ 
                            borderRadius: '12px', 
                            border: 'none', 
                            boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                            fontSize: '10px',
                            fontWeight: 'bold'
                        }}
                    />
                    <Bar dataKey="total" radius={[8, 8, 0, 0]} barSize={40}>
                        {chartData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[entry.key] || '#cbd5e1'} />
                        ))}
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}

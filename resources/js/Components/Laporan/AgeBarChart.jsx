import React from 'react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Cell } from 'recharts';

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

export default function AgeBarChart({ data }) {
    // Format data from Laravel age groups
    const safeData = data ?? [];
    let chartData = [];

    if (Array.isArray(safeData)) {
        chartData = safeData.map(item => ({
            name: (item.age_group || '').split(' (')[0],
            full_name: item.age_group,
            value: item.total
        }));
    } else {
        chartData = Object.entries(safeData).map(([name, value]) => ({
            name: name.split(' (')[0],
            full_name: name,
            value: value
        }));
    }

    return (
        <div className="h-[300px] w-full">
            <ResponsiveContainer width="100%" height="100%" minWidth={1} minHeight={1}>
                <BarChart
                    layout="vertical"
                    data={chartData}
                    margin={{ top: 5, right: 30, left: 40, bottom: 5 }}
                >
                    <CartesianGrid strokeDasharray="3 3" horizontal={true} vertical={false} stroke="#f3f4f6" />
                    <XAxis type="number" hide />
                    <YAxis 
                        dataKey="name" 
                        type="category" 
                        axisLine={false}
                        tickLine={false}
                        tick={{ fontSize: 9, fontWeight: 'bold', fill: '#9ca3af' }}
                        width={80}
                    />
                    <Tooltip 
                        cursor={{ fill: '#f9fafb' }}
                        contentStyle={{ 
                            borderRadius: '12px', 
                            border: 'none', 
                            boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                            fontSize: '10px',
                            fontWeight: 'bold'
                        }}
                        labelStyle={{ color: '#374151', marginBottom: '4px' }}
                    />
                    <Bar dataKey="value" radius={[0, 8, 8, 0]} barSize={24}>
                        {chartData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                        ))}
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}

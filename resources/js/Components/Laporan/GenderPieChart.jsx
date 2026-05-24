import React from 'react';
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip, Legend } from 'recharts';

const COLORS = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];

export default function GenderPieChart({ data }) {
    // Determine format and extract totals
    let laki = 0;
    let perempuan = 0;

    if (Array.isArray(data)) {
        laki = data.find(d => ['Laki-laki', 'L', 'LAKI-LAKI'].includes(d.jenis_kelamin))?.total || 0;
        perempuan = data.find(d => ['Perempuan', 'P', 'PEREMPUAN'].includes(d.jenis_kelamin))?.total || 0;
    } else if (typeof data === 'object' && data !== null) {
        laki = data['LAKI-LAKI'] || data['L'] || 0;
        perempuan = data['PEREMPUAN'] || data['P'] || 0;
    }

    const chartData = [
        { name: 'Laki-laki', value: laki },
        { name: 'Perempuan', value: perempuan },
    ].filter(d => d.value > 0);

    return (
        <div className="h-[300px] w-full">
            <ResponsiveContainer width="100%" height="100%" minWidth={1} minHeight={1}>
                <PieChart>
                    <Pie
                        data={chartData}
                        cx="50%"
                        cy="50%"
                        innerRadius={60}
                        outerRadius={80}
                        paddingAngle={5}
                        dataKey="value"
                    >
                        {chartData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                        ))}
                    </Pie>
                    <Tooltip 
                        contentStyle={{ 
                            borderRadius: '12px', 
                            border: 'none', 
                            boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                            fontSize: '10px',
                            fontWeight: 'bold',
                            textTransform: 'uppercase'
                        }} 
                    />
                    <Legend 
                        verticalAlign="bottom" 
                        height={36}
                        formatter={(value) => <span className="text-[10px] font-black text-gray-500 uppercase tracking-widest">{value}</span>}
                    />
                </PieChart>
            </ResponsiveContainer>
        </div>
    );
}

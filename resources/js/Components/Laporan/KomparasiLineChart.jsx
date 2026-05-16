import React from 'react';
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';

export default function KomparasiLineChart({ data }) {
    const chartData = Array.isArray(data) ? data : [];

    return (
        <div className="h-[350px] w-full">
            <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={chartData} margin={{ top: 10, right: 30, left: 0, bottom: 0 }}>
                    <defs>
                        <linearGradient id="colorPenduduk" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="5%" stopColor="#10b981" stopOpacity={0.1}/>
                            <stop offset="95%" stopColor="#10b981" stopOpacity={0}/>
                        </linearGradient>
                        <linearGradient id="colorMutasi" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="5%" stopColor="#3b82f6" stopOpacity={0.1}/>
                            <stop offset="95%" stopColor="#3b82f6" stopOpacity={0}/>
                        </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                    <XAxis 
                        dataKey="name" 
                        axisLine={false} 
                        tickLine={false} 
                        tick={{ fontSize: 9, fontWeight: 'bold', fill: '#9ca3af' }}
                    />
                    <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 9, fill: '#9ca3af' }} />
                    <Tooltip 
                        contentStyle={{ 
                            borderRadius: '12px', 
                            border: 'none', 
                            boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                            fontSize: '10px',
                            fontWeight: 'bold'
                        }}
                    />
                    <Legend 
                        verticalAlign="top" 
                        align="right"
                        height={36}
                        iconType="circle"
                        formatter={(value) => <span className="text-[10px] font-black text-gray-500 uppercase tracking-widest">{value}</span>}
                    />
                    <Area 
                        type="monotone" 
                        dataKey="penduduk" 
                        name="Penduduk Baru"
                        stroke="#10b981" 
                        strokeWidth={3}
                        fillOpacity={1} 
                        fill="url(#colorPenduduk)" 
                    />
                    <Area 
                        type="monotone" 
                        dataKey="mutasi" 
                        name="Total Mutasi"
                        stroke="#3b82f6" 
                        strokeWidth={3}
                        fillOpacity={1} 
                        fill="url(#colorMutasi)" 
                    />
                </AreaChart>
            </ResponsiveContainer>
        </div>
    );
}

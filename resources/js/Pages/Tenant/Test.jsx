import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, Home, TrendingUp, AlertCircle } from 'lucide-react';

export default function Test() {
    const stats = [
        { label: 'Total Penduduk', value: '4,521', icon: Users, color: 'text-blue-600', bg: 'bg-blue-100' },
        { label: 'Total KK', value: '1,240', icon: Home, color: 'text-green-600', bg: 'bg-green-100' },
        { label: 'Pertumbuhan', value: '+2.4%', icon: TrendingUp, color: 'text-purple-600', bg: 'bg-purple-100' },
        { label: 'KK Bermasalah', value: '12', icon: AlertCircle, color: 'text-red-600', bg: 'bg-red-100' },
    ];

    return (
        <AuthenticatedLayout title="Dashboard Test">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {stats.map((stat, i) => (
                    <div key={i} className="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                        <div className="flex items-center justify-between mb-4">
                            <div className={`${stat.bg} p-3 rounded-2xl`}>
                                <stat.icon className={`w-6 h-6 ${stat.color}`} />
                            </div>
                            <span className="text-xs font-bold text-gray-400 uppercase tracking-widest">Live</span>
                        </div>
                        <h3 className="text-sm font-bold text-gray-500 uppercase tracking-wider">{stat.label}</h3>
                        <p className="text-3xl font-black text-gray-900 mt-1">{stat.value}</p>
                    </div>
                ))}
            </div>

            <div className="mt-8 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 className="text-xl font-bold text-gray-900 mb-4">Selamat Datang di SID Cibatu</h2>
                <p className="text-gray-500 leading-relaxed">
                    Ini adalah tampilan dashboard versi React (Inertia). Desain ini dibuat lebih modern, bersih, dan premium untuk memudahkan pengelolaan data desa. Silakan jelajahi menu di sebelah kiri untuk melihat navigasi yang baru.
                </p>
                <div className="mt-6 flex gap-3">
                    <button className="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-green-100">
                        Mulai Kelola Data
                    </button>
                    <button className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-2xl font-bold transition-all">
                        Panduan Sistem
                    </button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import { Head } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, StatCard } from '@/Components/Shared';
import { Building2, CheckCircle2, Users, HardDrive, Shield } from 'lucide-react';

export default function Index({ stats }) {
    return (
        <LandlordLayout>
            <Head title="Landlord Dashboard" />

            <div className="space-y-8">
                {/* Header */}
                <PageHeader 
                    icon={Shield}
                    title="Landlord Dashboard"
                    subtitle="Selamat Datang di Panel Manajemen Pusat SaaS Sistem Desa Terpadu."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {/* Stats Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatCard 
                        icon={Building2}
                        label="Total Desa"
                        value={stats.total_tenants}
                        color="blue"
                        badge="Tenant"
                    />
                    <StatCard 
                        icon={CheckCircle2}
                        label="Desa Aktif"
                        value={stats.active_tenants}
                        color="green"
                        badge="Status"
                    />
                    <StatCard 
                        icon={Users}
                        label="Total Kuota User"
                        value={stats.total_users_limit}
                        color="purple"
                        badge="Limit"
                    />
                    <StatCard 
                        icon={HardDrive}
                        label="Total Storage"
                        value={`${(stats.total_storage_limit / 1024).toFixed(1)} GB`}
                        color="yellow"
                        badge="Disk"
                    />
                </div>

                {/* Info Card */}
                <div className="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
                    <div className="space-y-2 max-w-2xl">
                        <h3 className="text-xl font-black text-slate-800 uppercase italic tracking-tight">Koneksi Central Aktif</h3>
                        <p className="text-slate-600 text-sm leading-relaxed">
                            Saat ini Anda terhubung langsung dengan basis data utama (<code className="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600 text-xs">db_central</code>). 
                            Segala perubahan pada menu <strong>Manajemen Desa</strong> atau <strong>Alokasi Resource</strong> akan berdampak langsung ke seluruh ekosistem tenant desa secara real-time.
                        </p>
                    </div>
                    <div className="px-6 py-3 bg-indigo-50 border border-indigo-100 rounded-2xl text-xs font-black text-indigo-700 uppercase tracking-widest italic shrink-0">
                        Central Context
                    </div>
                </div>
            </div>
        </LandlordLayout>
    );
}

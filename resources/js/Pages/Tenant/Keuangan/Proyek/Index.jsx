import React, { useState } from 'react';
import { Head, Link, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import ProyekCard from '@/Components/Keuangan/ProyekCard';
import RealisasiModal from '@/Components/Keuangan/RealisasiModal';
import KeuanganFilters from '@/Components/Keuangan/KeuanganFilters';
import KeuanganStats from '@/Components/Keuangan/KeuanganStats';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Building2, Plus, ArrowLeft } from 'lucide-react';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, filters = {}, proyek, stats }) {
    const [selectedProyek, setSelectedProyek] = useState(null);

    return (
        <AuthenticatedLayout user={auth.user} title="Proyek Desa">
            <Head title="Proyek Desa - Admin Panel" />

            {/* Realisasi Modal */}
            {selectedProyek && (
                <RealisasiModal
                    proyek={selectedProyek}
                    onClose={() => setSelectedProyek(null)}
                />
            )}

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader
                    title="Proyek Desa"
                    subtitle="Manajemen & Monitoring Proyek Pembangunan"
                    icon={Building2}
                    actions={[
                        { label: 'DASHBOARD', icon: ArrowLeft, href: route('transparansi-desa.index'), variant: 'ghost' },
                        { label: 'TAMBAH PROYEK', icon: Plus, href: route('anggaran.create-proyek'), variant: 'white' },
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <KeuanganStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <KeuanganFilters
                    filters={filters}
                    tahunList={[]}
                    routeName="transparansi-desa.proyek"
                    showSumberDana={false}
                    showJenis={true}
                    showStatus={true}
                />

                {/* Grid */}
                <Deferred data="proyek" fallback={<SkeletonTable columns={3} rows={6} />}>
                    {proyek?.data?.length > 0 ? (
                        <>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                {proyek.data.map((p) => (
                                    <ProyekCard
                                        key={p.id}
                                        proyek={p}
                                        onUpdateRealisasi={setSelectedProyek}
                                    />
                                ))}
                            </div>
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                                <Pagination links={proyek?.links} from={proyek?.from} to={proyek?.to} total={proyek?.total} />
                            </div>
                        </>
                    ) : (
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                            <div className="w-56 h-56 mx-auto mb-4">
                                <LottieComponent animationData={noDataAnimation} loop />
                            </div>
                            <h3 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter">Belum Ada Proyek Desa</h3>
                            <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2 mb-8">Mulai dengan menambahkan proyek pembangunan desa pertama</p>
                            <Link href={route('anggaran.create-proyek')} className="inline-flex items-center px-8 py-4 bg-green-600 text-white rounded-2xl text-xs font-black shadow-xl shadow-green-200 hover:bg-green-700 transition-all uppercase tracking-widest">
                                <Plus className="w-4 h-4 mr-2" /> TAMBAH PROYEK PERTAMA
                            </Link>
                        </div>
                    )}
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}

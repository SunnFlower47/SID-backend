import React, { useState } from 'react';
import { Head, Link, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Building2 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Proyek Desa</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Manajemen & Monitoring Proyek Pembangunan</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <Link href={route('transparansi-desa.index')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" /> DASHBOARD
                            </Link>
                            <Link href={route('anggaran.create-proyek')} className="flex items-center px-4 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest">
                                <Plus className="w-3.5 h-3.5 mr-2" /> TAMBAH PROYEK
                            </Link>
                        </div>
                    </div>
                </div>

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

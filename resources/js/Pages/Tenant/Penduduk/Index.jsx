import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ResidentStats from '@/Components/Penduduk/ResidentStats';
import ResidentFilters from '@/Components/Penduduk/ResidentFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Users, Plus, FileSpreadsheet, Crown, Heart, User, Trash2 } from 'lucide-react';
import Swal from 'sweetalert2';
import axios from 'axios';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';

// Shared Components
import { PageHeader, TableCard, EmptyState, ActionButtons, Badge } from '@/Components/Shared';
import { useSwalDelete } from '@/lib/useSwalDelete';

// Kadang di Vite/React 19, import default terbaca sebagai object { default: ... }
const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, penduduks, stats, rtList, rwList, dusunList, filters }) {
    const [isExporting, setIsExporting] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);
    const confirmDelete = useSwalDelete();
    
    const handleExport = async () => {
        setIsExporting(true);

        try {
            const params = new URLSearchParams(window.location.search);
            params.delete('page');
            
            const response = await axios.get(route('penduduk.export.excel'), {
                params: Object.fromEntries(params),
                responseType: 'blob'
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Data_Penduduk_${new Date().toLocaleDateString('id-ID')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            // Show success animation
            setShowSuccess(true);
            setTimeout(() => setShowSuccess(false), 3000);
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengekspor data.', 'error');
        } finally {
            setIsExporting(false);
        }
    };

    const handleDelete = (id, nama) => {
        confirmDelete(nama, () => {
            router.delete(route('penduduk.destroy', id), {
                preserveScroll: true,
            });
        });
    };

    const getKedudukanStyle = (kedudukan) => {
        const k = (kedudukan || '').toUpperCase();
        if (k === 'KEPALA KELUARGA') return { color: 'blue', icon: Crown };
        if (k === 'ISTRI') return { color: 'pink', icon: Heart };
        if (k === 'ANAK') return { color: 'green', icon: User };
        return { color: 'gray', icon: User };
    };

    let currentKK = null;

    return (
        <AuthenticatedLayout user={auth.user} title="Data Penduduk">
            
            {/* Custom Loading Overlay */}
            {isExporting && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white rounded-3xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full mx-4 animate-in zoom-in-95 duration-300">
                        <div className="w-24 h-24">
                            <LottieComponent animationData={loadingAnimation} loop={true} />
                        </div>
                        <div className="text-center">
                            <h3 className="text-lg font-black text-gray-900">Mengekspor Data</h3>
                            <p className="text-sm text-gray-500 mt-1">Mohon tunggu, file Excel sedang disiapkan...</p>
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header (Refactored) */}
                <PageHeader 
                    title="Data Penduduk"
                    subtitle="Kelola data warga Desa Cibatu"
                    icon={Users}
                    actions={[
                        {
                            label: 'EXCEL',
                            icon: FileSpreadsheet,
                            onClick: handleExport,
                            loading: isExporting,
                            variant: 'ghost'
                        },
                        {
                            label: 'TAMBAH',
                            icon: Plus,
                            href: route('penduduk.create'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Statistics */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <ResidentStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <ResidentFilters 
                    filters={filters} 
                    rtList={rtList} 
                    rwList={rwList} 
                    dusunList={dusunList} 
                />

                {/* Data Table (Refactored) */}
                <Deferred data="penduduks" fallback={<SkeletonTable columns={6} rows={10} />}>
                    <TableCard
                        title="Daftar Warga"
                        icon={Users}
                        total={penduduks?.total || 0}
                        totalLabel=""
                        pagination={penduduks}
                        noPadding={true}
                    >
                        {penduduks?.data?.length > 0 ? (
                        <>
                            {/* Desktop Table */}
                            <div className="hidden lg:block overflow-x-auto">
                                <table className="w-full text-left text-sm text-gray-600">
                                    <thead className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                                        <tr>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">NOMOR URUT</th>
                                            <th rowSpan="2" className="px-4 py-3 align-middle text-center border-r border-gray-200">AKSI</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NAMA LENGKAP / PANGGILAN</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">JENIS KELAMIN</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">STATUS PERKAWINAN</th>
                                            <th colSpan="2" className="px-4 py-2 border-r border-b border-gray-200 text-center">TEMPAT & TANGGAL LAHIR</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">AGAMA</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">PENDIDIKAN TERAKHIR</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">PEKERJAAN</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">DAPAT MEMBACA HURUF</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle text-center">KEWARGANEGARAAN</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">ALAMAT LENGKAP</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">KEDUDUKAN DLM KELUARGA</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NIK</th>
                                            <th rowSpan="2" className="px-4 py-3 border-r border-gray-200 align-middle">NO. KK</th>
                                        </tr>
                                        <tr>
                                            <th className="px-4 py-2 border-r border-gray-200 text-center">TEMPAT LAHIR</th>
                                            <th className="px-4 py-2 border-r border-gray-200 text-center">TGL</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50 whitespace-nowrap">
                                        {penduduks.data.map((p, index) => {
                                            const isNewFamily = currentKK !== p.nkk;
                                            currentKK = p.nkk;
                                            const style = getKedudukanStyle(p.kedudukan_keluarga);
                                            const isKepala = (p.kedudukan_keluarga || '').toUpperCase() === 'KEPALA KELUARGA';
                                            const nomorUrut = penduduks.from ? penduduks.from + index : index + 1;

                                            return (
                                                <React.Fragment key={p.id}>
                                                    {isNewFamily && index > 0 && (
                                                        <tr><td colSpan="16"><div className="h-2 bg-gray-50/50"></div></td></tr>
                                                    )}
                                                    <tr className={`hover:bg-blue-50/30 transition-colors ${isNewFamily ? 'bg-green-50/20' : ''} ${isKepala ? 'bg-blue-50/20' : ''}`}>
                                                        <td className="px-4 py-3 text-center font-mono text-xs">{nomorUrut}</td>
                                                        <td className="px-4 py-3 text-center border-r border-gray-50">
                                                            <ActionButtons 
                                                                viewHref={route('penduduk.show', p.id)}
                                                                editHref={route('penduduk.edit', p.id)}
                                                                onDelete={() => handleDelete(p.id, p.nama)}
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 font-bold text-gray-900">
                                                            <div className="flex items-center gap-2">
                                                                <div className={`w-6 h-6 rounded-full flex items-center justify-center shrink-0 ${isKepala ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-500'}`}>
                                                                    {isKepala ? <Crown className="w-3 h-3" /> : <User className="w-3 h-3" />}
                                                                </div>
                                                                {p.nama}
                                                            </div>
                                                        </td>
                                                        <td className="px-4 py-3 text-center font-bold">{p.jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P'}</td>
                                                        <td className="px-4 py-3">{p.status_perkawinan}</td>
                                                        <td className="px-4 py-3">{p.tempat_lahir}</td>
                                                        <td className="px-4 py-3">{p.tanggal_lahir ? p.tanggal_lahir.split('T')[0].split('-').reverse().join('-') : '-'}</td>
                                                        <td className="px-4 py-3">{p.agama}</td>
                                                        <td className="px-4 py-3">{p.pendidikan}</td>
                                                        <td className="px-4 py-3">{p.pekerjaan}</td>
                                                        <td className="px-4 py-3 text-center">{p.dapat_membaca_huruf || '-'}</td>
                                                        <td className="px-4 py-3 text-center">{p.kewarganegaraan || 'WNI'}</td>
                                                        <td className="px-4 py-3 text-xs max-w-[200px] truncate" title={`${p.alamat} RT ${p.rt_label}/RW ${p.rw_label}`}>
                                                            {p.alamat} RT {p.rt_label}/RW {p.rw_label}
                                                        </td>
                                                        <td className="px-4 py-3">
                                                            <Badge color={style.color} icon={style.icon} size="sm">
                                                                {p.kedudukan_keluarga}
                                                            </Badge>
                                                        </td>
                                                        <td className="px-4 py-3 font-mono text-xs">{p.nik}</td>
                                                        <td className="px-4 py-3 font-mono text-xs text-green-700 font-bold bg-green-50/50 rounded px-1">{p.nkk}</td>
                                                    </tr>
                                                </React.Fragment>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile List View */}
                            <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                {penduduks.data.map(p => {
                                    const style = getKedudukanStyle(p.kedudukan_keluarga);
                                    
                                    return (
                                    <div key={p.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                        <div className="flex items-start gap-4 mb-4">
                                            <div className="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                                <User className="w-6 h-6" />
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <h4 className="font-black text-gray-900 truncate">{p.nama}</h4>
                                                <div className="flex items-center gap-2 mt-1">
                                                    <Badge color={style.color} size="sm">
                                                        {p.kedudukan_keluarga}
                                                    </Badge>
                                                    <span className="text-xs font-medium text-gray-500">{p.usia} thn</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div className="grid grid-cols-2 gap-3 mb-4">
                                            <div className="bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NIK</p>
                                                <p className="text-xs font-mono font-bold text-gray-900">{p.nik}</p>
                                            </div>
                                            <div className="bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NO KK</p>
                                                <p className="text-xs font-mono font-bold text-green-700">{p.nkk}</p>
                                            </div>
                                            <div className="col-span-2 bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">ALAMAT</p>
                                                <p className="text-xs font-medium text-gray-900 truncate">{p.alamat}</p>
                                                <p className="text-[10px] text-gray-500 mt-0.5">RT {p.rt_label}/RW {p.rw_label}</p>
                                            </div>
                                        </div>

                                        <div className="flex gap-2">
                                            <Link href={route('penduduk.show', p.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                DETAIL
                                            </Link>
                                            <Link href={route('penduduk.edit', p.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                EDIT
                                            </Link>
                                            <button onClick={() => handleDelete(p.id, p.nama)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                )})}
                            </div>
                        </>
                        ) : (
                            <EmptyState 
                                title="Belum Ada Data Warga"
                                message="Daftar penduduk masih kosong. Silakan tambah data warga baru untuk memulai pengelolaan kependudukan."
                                action={{
                                    label: "TAMBAH WARGA SEKARANG",
                                    href: route('penduduk.create'),
                                    icon: Plus
                                }}
                            />
                        )}
                    </TableCard>
                </Deferred>
            </div>

            {/* Success Animation Overlay */}
            {showSuccess && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center animate-in zoom-in duration-300">
                        <div className="w-48 h-48">
                            <LottieComponent animationData={successAnimation} loop={false} />
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mt-4 uppercase italic tracking-tighter">Export Berhasil!</h3>
                        <p className="text-sm text-gray-500 font-bold uppercase tracking-widest mt-1">Data Anda sudah siap.</p>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}

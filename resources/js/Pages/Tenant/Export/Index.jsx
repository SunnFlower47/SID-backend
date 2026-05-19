import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import * as Icons from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';

const LottieComponent = Lottie?.default || Lottie;

export default function ExportData() {
    const [exportingId, setExportingId] = useState(null);
    const [showSuccess, setShowSuccess] = useState(false);

    const exportItems = [
        {
            id: 'penduduk',
            title: 'Data Penduduk',
            description: 'Export data penduduk ke Excel',
            href: route('export.penduduk'),
            icon: 'Users',
            colors: 'from-blue-500 to-blue-600',
            bgLight: 'bg-blue-50',
            border: 'border-blue-200'
        },
        {
            id: 'kk',
            title: 'Kartu Keluarga',
            description: 'Export data KK ke Excel',
            href: route('export.kartu-keluarga'),
            icon: 'Home',
            colors: 'from-green-500 to-green-600',
            bgLight: 'bg-green-50',
            border: 'border-green-200'
        },
        {
            id: 'bansos',
            title: 'Bantuan Sosial',
            description: 'Export data bantuan sosial',
            href: route('export.bantuan-sosial'),
            icon: 'Heart',
            colors: 'from-red-500 to-red-600',
            bgLight: 'bg-red-50',
            border: 'border-red-200'
        },
        {
            id: 'penerima_bansos',
            title: 'Penerima Bantuan',
            description: 'Export data penerima bantuan',
            href: route('export.penerima-bantuan-sosial'),
            icon: 'HeartHandshake',
            colors: 'from-yellow-500 to-yellow-600',
            bgLight: 'bg-yellow-50',
            border: 'border-yellow-200'
        },
        {
            id: 'pengaduan',
            title: 'Pengaduan',
            description: 'Export data pengaduan warga',
            href: route('export.pengaduan'),
            icon: 'MessageSquare',
            colors: 'from-cyan-500 to-cyan-600',
            bgLight: 'bg-cyan-50',
            border: 'border-cyan-200'
        },
        {
            id: 'umkm',
            title: 'Data UMKM',
            description: 'Export data UMKM desa',
            href: route('export.umkm'),
            icon: 'Store',
            colors: 'from-purple-500 to-purple-600',
            bgLight: 'bg-purple-50',
            border: 'border-purple-200'
        },
        {
            id: 'surat',
            title: 'Surat Pengajuan',
            description: 'Export data surat pengajuan',
            href: route('export.surat-pengajuan'),
            icon: 'FileText',
            colors: 'from-gray-500 to-gray-600',
            bgLight: 'bg-gray-50',
            border: 'border-gray-200'
        },
    ];

    const handleExport = async (item) => {
        setExportingId(item.id);

        try {
            const response = await axios.get(item.href, {
                responseType: 'blob'
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            const dateStr = new Date().toLocaleDateString('id-ID').replace(/\//g, '-');
            const safeTitle = item.title.replace(/\s+/g, '_');
            link.setAttribute('download', `Export_${safeTitle}_${dateStr}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            // Show success animation
            setShowSuccess(true);
            setTimeout(() => setShowSuccess(false), 3000);
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire('Gagal!', `Terjadi kesalahan saat mengekspor ${item.title}.`, 'error');
        } finally {
            setExportingId(null);
        }
    };

    return (
        <AuthenticatedLayout title="Export Data">
            <Head title="Export Data" />

            {/* Custom Loading Overlay */}
            {exportingId && (
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

            <div className="space-y-6">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Icons.Download className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Export Data</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Unduh data sistem ke format Excel
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Export Section */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                        <h3 className="text-lg font-bold text-gray-800 flex items-center uppercase italic tracking-tighter">
                            <Icons.FileSpreadsheet className="w-5 h-5 text-green-600 mr-3" />
                            Pilih Data Export
                        </h3>
                    </div>
                    <div className="p-6">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            {exportItems.map((item, idx) => {
                                const Icon = Icons[item.icon] || Icons.File;
                                const isExportingThis = exportingId === item.id;
                                return (
                                    <div key={idx} className={cn("rounded-2xl p-4 border transition-all duration-300 hover:shadow-md group flex flex-col h-full", item.border, item.bgLight)}>
                                        <div className="flex items-center mb-3">
                                            <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center mr-3 bg-gradient-to-r shadow-sm", item.colors)}>
                                                <Icon className="w-5 h-5 text-white" />
                                            </div>
                                            <h6 className="text-sm font-bold text-gray-900 leading-tight">{item.title}</h6>
                                        </div>
                                        <p className="text-gray-600 text-[11px] font-medium mb-4 flex-grow">{item.description}</p>
                                        <button 
                                            type="button"
                                            onClick={() => handleExport(item)}
                                            disabled={exportingId !== null}
                                            className={cn(
                                                "inline-flex items-center justify-center w-full px-3 py-2 text-xs font-bold text-white rounded-xl transition-all duration-300 shadow-sm bg-gradient-to-r",
                                                exportingId !== null ? "opacity-50 cursor-not-allowed" : "hover:shadow active:scale-95",
                                                item.colors
                                            )}
                                        >
                                            {isExportingThis ? (
                                                <Icons.Loader2 className="w-3.5 h-3.5 mr-1.5 animate-spin" />
                                            ) : (
                                                <Icons.Download className="w-3.5 h-3.5 mr-1.5" />
                                            )}
                                            {isExportingThis ? 'Mengekspor...' : 'Download Excel'}
                                        </button>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>

                {/* Instructions */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="bg-blue-50/50 px-6 py-4 border-b border-blue-100">
                        <h3 className="text-sm font-bold text-blue-800 flex items-center">
                            <Icons.Info className="w-4 h-4 text-blue-600 mr-2" />
                            Petunjuk Export Data
                        </h3>
                    </div>
                    <div className="p-6">
                        <ul className="space-y-3">
                            {[
                                'Data akan diekspor dalam format Excel (.xlsx)',
                                'File akan otomatis terunduh saat tombol diklik',
                                'Pastikan Anda memiliki aplikasi pembaca spreadsheet (Microsoft Excel / Google Sheets)',
                                'Untuk Import data, gunakan menu "Import Data" yang terpisah'
                            ].map((text, i) => (
                                <li key={i} className="flex items-start">
                                    <Icons.CheckCircle2 className="w-4 h-4 text-green-500 mr-3 shrink-0 mt-0.5" />
                                    <span className="text-sm font-medium text-gray-600">{text}</span>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

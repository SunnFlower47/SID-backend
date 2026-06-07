import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import * as Icons from 'lucide-react';
import { PageHeader } from '@/Components/Shared';
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
        {
            id: 'aset',
            title: 'Data Aset Desa',
            description: 'Export data inventaris aset desa',
            href: route('export.aset'),
            icon: 'Box',
            colors: 'from-amber-500 to-amber-600',
            bgLight: 'bg-amber-50',
            border: 'border-amber-200',
            hasYearFilter: true
        },
    ];

    const handleExport = async (item) => {
        let finalHref = item.href;

        if (item.id === 'aset') {
            const { value: formValues } = await Swal.fire({
                title: 'Pilih Periode Laporan',
                html: `
                    <div class="flex flex-col gap-4 mt-4 text-left">
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">Tahun Anggaran</label>
                            <input id="swal-input-year" type="number" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-shadow bg-gray-50 text-gray-900 font-medium" 
                                value="${new Date().getFullYear()}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">Pilih Semester</label>
                            <div class="relative">
                                <select id="swal-input-semester" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-shadow bg-gray-50 text-gray-900 font-medium appearance-none">
                                    <option value="1">Semester 1 (Jan - Jun)</option>
                                    <option value="2">Semester 2 (Jul - Des)</option>
                                    <option value="gabung">1 Tahun Penuh (Gabungan)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '<div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Download</div>',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'font-sans',
                    popup: 'rounded-3xl p-6 shadow-2xl border border-gray-100 max-w-sm',
                    title: 'text-2xl font-black text-gray-800',
                    confirmButton: 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-transform hover:scale-105 active:scale-95 border-none',
                    cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-xl transition-colors border-none'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    const year = document.getElementById('swal-input-year').value;
                    const semester = document.getElementById('swal-input-semester').value;
                    if (!year) {
                        Swal.showValidationMessage('Tahun harus diisi!');
                        return null;
                    }
                    return { year, semester };
                }
            });

            if (!formValues) return;
            finalHref = `${item.href}?tahun=${formValues.year}&semester=${formValues.semester}`;
        } else if (item.hasYearFilter) {
            const { value: year } = await Swal.fire({
                title: 'Pilih Tahun',
                input: 'number',
                inputLabel: 'Tahun Laporan',
                inputValue: new Date().getFullYear(),
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Tahun harus diisi!';
                    }
                }
            });

            if (!year) return; // cancelled
            
            finalHref = `${item.href}?tahun=${year}`;
        }

        setExportingId(item.id);

        try {
            const response = await axios.get(finalHref, {
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
                <PageHeader
                    title="Export Data"
                    subtitle="Unduh data sistem ke format Excel"
                    icon={Icons.Download}
                    titleSize="lg"
                />

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

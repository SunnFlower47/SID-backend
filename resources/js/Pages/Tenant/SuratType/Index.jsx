import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import * as LucideIcons from 'lucide-react';
import { FileText, Plus, Edit, Trash2, Search, CheckCircle2, XCircle, Layout, Palette, Type, Info, HelpCircle } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

// Shared Components
import { PageHeader } from '@/Components/Shared';

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, suratTypes, storageInfo }) {
    const [searchQuery, setSearchQuery] = useState('');

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus jenis surat <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('admin.surat-type.destroy', id), {
                    preserveScroll: true
                });
            }
        });
    };

    const filteredSuratTypes = suratTypes.filter(type => 
        type.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
        type.kode.toLowerCase().includes(searchQuery.toLowerCase()) ||
        type.id.toLowerCase().includes(searchQuery.toLowerCase())
    );

    // Icon mapping logic
    const getIcon = (iconName) => {
        if (!iconName) return <FileText className="w-6 h-6" />;
        
        // Render dinamik menggunakan LucideIcons
        const IconComponent = LucideIcons[iconName] || FileText;
        return <IconComponent className="w-6 h-6" />;
    };

    const getColorClasses = (colorName, isActive) => {
        if (!isActive) return "bg-gray-50 text-gray-400";
        
        const colors = {
            blue: "bg-blue-50 text-blue-600",
            green: "bg-green-50 text-green-600",
            purple: "bg-purple-50 text-purple-600",
            orange: "bg-orange-50 text-orange-600",
            red: "bg-red-50 text-red-600",
            pink: "bg-pink-50 text-pink-600",
            yellow: "bg-yellow-50 text-yellow-600",
        };
        return colors[colorName] || "bg-green-50 text-green-600";
    };

    const getHoverTitleClass = (colorName) => {
        const colors = {
            blue: "group-hover:text-blue-700",
            green: "group-hover:text-green-700",
            purple: "group-hover:text-purple-700",
            orange: "group-hover:text-orange-700",
            red: "group-hover:text-red-700",
            pink: "group-hover:text-pink-700",
            yellow: "group-hover:text-yellow-700",
        };
        return colors[colorName] || "group-hover:text-green-700";
    };

    const getBorderBottomClass = (colorName, isActive) => {
        if (!isActive) return "bg-gray-200 h-1";
        
        const colors = {
            blue: "bg-blue-500 h-1",
            green: "bg-green-500 h-1",
            purple: "bg-purple-500 h-1",
            orange: "bg-orange-500 h-1",
            red: "bg-red-500 h-1",
            pink: "bg-pink-500 h-1",
            yellow: "bg-yellow-500 h-1",
        };
        return colors[colorName] || "bg-green-500 h-1";
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Master Jenis Surat">
            <Head title="Master Jenis Surat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title="Master Jenis Surat"
                    subtitle="Konfigurasi Template & Form Surat Dinamis"
                    icon={FileText}
                    actions={[
                        {
                            label: 'PANDUAN SURAT',
                            href: route('admin.surat-type.panduan'),
                            icon: HelpCircle,
                            variant: 'ghost'
                        },
                        {
                            label: 'TAMBAH JENIS',
                            href: route('admin.surat-type.create'),
                            icon: Plus,
                            variant: 'white'
                        }
                    ]}
                />
                
                {/* Storage Info + Info Box */}
                <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    {/* Storage Cards */}
                    <div className="bg-white border border-gray-100 rounded-3xl p-5 shadow-sm flex flex-col gap-1">
                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">File Aktif</p>
                        <p className="text-2xl font-black text-green-600">{storageInfo?.active_files ?? 0}</p>
                        <p className="text-[10px] text-gray-400 font-bold">template terhubung ke DB</p>
                    </div>
                    <div className={`border rounded-3xl p-5 shadow-sm flex flex-col gap-1 ${
                        storageInfo?.orphan_files > 0 ? 'bg-orange-50 border-orange-100' : 'bg-white border-gray-100'
                    }`}>
                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">File Tidak Terpakai</p>
                        <p className={`text-2xl font-black ${storageInfo?.orphan_files > 0 ? 'text-orange-500' : 'text-gray-300'}`}>
                            {storageInfo?.orphan_files ?? 0}
                        </p>
                        {storageInfo?.orphan_files > 0 ? (
                            <Link 
                                href={route('admin.surat-type.cleanup-templates')}
                                method="post"
                                as="button"
                                className="mt-1 w-full bg-orange-100 hover:bg-orange-200 text-orange-700 py-1.5 px-3 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all"
                            >
                                Hapus File Ga Kepake
                            </Link>
                        ) : (
                            <p className="text-[10px] text-gray-400 font-bold mt-1">storage bersih</p>
                        )}
                    </div>
                    <div className="bg-white border border-gray-100 rounded-3xl p-5 shadow-sm flex flex-col gap-1">
                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Ukuran</p>
                        <p className="text-2xl font-black text-blue-600">
                            {storageInfo?.total_size_kb >= 1024
                                ? `${(storageInfo.total_size_kb / 1024).toFixed(1)} MB`
                                : `${storageInfo?.total_size_kb ?? 0} KB`
                            }
                        </p>
                        <p className="text-[10px] text-gray-400 font-bold">semua file di storage</p>
                    </div>
                    {/* Info system */}
                    <div className="bg-green-50 border border-green-100 rounded-3xl p-5 flex items-start gap-3 sm:col-span-1">
                        <div className="p-1.5 bg-green-100 text-green-600 rounded-xl shrink-0">
                            <Info className="w-4 h-4" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-green-800 uppercase tracking-widest mb-1 italic">Auto-Cleanup Aktif</p>
                            <p className="text-[10px] text-green-700/80 font-medium leading-relaxed">
                                File lama otomatis terhapus saat template diganti atau jenis surat dihapus.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Search & Filter Bar */}
                <div className="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-sm gap-4">
                    <div className="relative w-full sm:w-96">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Search className="w-4 h-4 text-gray-400" />
                        </div>
                        <input
                            type="text"
                            placeholder="Cari nama, kode, atau ID surat..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 transition-all"
                        />
                    </div>
                    <div className="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <CheckCircle2 className="w-4 h-4 text-green-500" />
                        <span>{suratTypes.filter(t => t.is_active).length} Aktif</span>
                        <span className="mx-2">|</span>
                        <XCircle className="w-4 h-4 text-red-400" />
                        <span>{suratTypes.filter(t => !t.is_active).length} Non-Aktif</span>
                    </div>
                </div>

                {/* Grid View */}
                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    {filteredSuratTypes.map((type) => (
                        <div key={type.id} className="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden flex flex-col">
                            <div className="p-6 flex-1">
                                <div className="flex justify-between items-start mb-4">
                                    <div className={`p-3 rounded-2xl ${getColorClasses(type.color, type.is_active)}`}>
                                        {getIcon(type.icon)}
                                    </div>
                                    <div className="flex gap-1">
                                        <Link 
                                            href={route('admin.surat-type.edit', type.id)}
                                            className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                        >
                                            <Edit className="w-4 h-4" />
                                        </Link>
                                        <button 
                                            onClick={() => handleDelete(type.id, type.nama)}
                                            className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                                
                                <div className="mb-4">
                                    <h3 className={`text-lg font-black text-gray-900 tracking-tight uppercase italic leading-tight transition-colors ${getHoverTitleClass(type.color)}`}>{type.nama}</h3>
                                    <div className="flex items-center gap-2 mt-1">
                                        <span className="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase tracking-wider">{type.id}</span>
                                        <span className="px-2 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-bold rounded uppercase tracking-wider">{type.kode}</span>
                                    </div>
                                </div>

                                <p className="text-sm text-gray-500 line-clamp-2 mb-6 font-medium leading-relaxed italic">
                                    {type.deskripsi || 'Tidak ada deskripsi untuk jenis surat ini.'}
                                </p>

                                <div className="grid grid-cols-2 gap-3 mt-auto">
                                    <div className="bg-gray-50 p-3 rounded-2xl border border-gray-100">
                                        <div className="flex items-center gap-2 mb-1">
                                            <Layout className="w-3 h-3 text-gray-400" />
                                            <span className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Template</span>
                                        </div>
                                        <span className={type.has_template ? "text-[10px] font-black text-green-600 uppercase tracking-widest" : "text-[10px] font-black text-gray-400 uppercase tracking-widest"}>
                                            {type.has_template ? 'TERSEDIA' : 'TIDAK ADA'}
                                        </span>
                                    </div>
                                    <div className="bg-gray-50 p-3 rounded-2xl border border-gray-100">
                                        <div className="flex items-center gap-2 mb-1">
                                            <Type className="w-3 h-3 text-gray-400" />
                                            <span className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Custom Form</span>
                                        </div>
                                        <span className={type.form_json && type.form_json.length > 0 ? "text-[10px] font-black text-blue-600 uppercase tracking-widest" : "text-[10px] font-black text-gray-400 uppercase tracking-widest"}>
                                            {type.form_json && type.form_json.length > 0 ? `${type.form_json.length} FIELD` : 'STANDAR'}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div className={getBorderBottomClass(type.color, type.is_active)}></div>
                        </div>
                    ))}
                </div>

                {filteredSuratTypes.length === 0 && (
                    <div className="bg-white rounded-3xl border border-gray-100 p-12 text-center shadow-sm">
                        <div className="w-48 h-48 mx-auto">
                            <LottieComponent animationData={noDataAnimation} loop={true} />
                        </div>
                        <h3 className="text-xl font-black text-gray-900 uppercase italic">Data Tidak Ditemukan</h3>
                        <p className="text-gray-400 font-bold text-xs uppercase tracking-widest mt-2">Coba kata kunci pencarian lain atau tambah jenis surat baru</p>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { FileText, Plus, Edit, Trash2, Search, CheckCircle2, XCircle, Layout, Palette, Type, Info, Users, Briefcase, MapPin, Heart, Baby, Ghost, ShieldAlert, School, Wallet, Home, Truck, Building, Skull, ClipboardList, Footprints } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

// Shared Components
import { PageHeader } from '@/Components/Shared';

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, suratTypes }) {
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
        
        // Bersihkan prefix FontAwesome jika ada (fas fa-baby -> baby)
        const cleanName = iconName.replace('fas fa-', '').replace('fa-', '').toLowerCase();

        const icons = {
            'filetext': <FileText className="w-6 h-6" />,
            'file-text': <FileText className="w-6 h-6" />,
            'file-alt': <FileText className="w-6 h-6" />,
            'plus': <Plus className="w-6 h-6" />,
            'users': <Users className="w-6 h-6" />,
            'briefcase': <Briefcase className="w-6 h-6" />,
            'mappin': <MapPin className="w-6 h-6" />,
            'map-pin': <MapPin className="w-6 h-6" />,
            'heart': <Heart className="w-6 h-6" />,
            'baby': <Baby className="w-6 h-6" />,
            'ghost': <Ghost className="w-6 h-6" />,
            'skull': <Skull className="w-6 h-6" />,
            'home': <Home className="w-6 h-6" />,
            'building': <Building className="w-6 h-6" />,
            'clipboardlist': <ClipboardList className="w-6 h-6" />,
            'clipboard-list': <ClipboardList className="w-6 h-6" />,
            'walking': <Footprints className="w-6 h-6" />,
            'hand-holding-heart': <Heart className="w-6 h-6" />,
            'school': <School className="w-6 h-6" />,
            'wallet': <Wallet className="w-6 h-6" />,
            'truck': <Truck className="w-6 h-6" />,
        };

        // Coba cari dengan nama asli atau nama yang sudah dibersihkan
        return icons[cleanName] || icons[iconName.toLowerCase()] || <FileText className="w-6 h-6" />;
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
                    actionLabel="TAMBAH JENIS"
                    actionHref={route('admin.surat-type.create')}
                    actionIcon={Plus}
                />
                
                {/* Info Box */}
                <div className="bg-green-50 border border-green-100 rounded-3xl p-6 flex items-start gap-4">
                    <div className="p-2 bg-green-100 text-green-600 rounded-xl">
                        <Info className="w-5 h-5" />
                    </div>
                    <div>
                        <p className="text-xs font-bold text-green-800 uppercase tracking-widest mb-1 italic">Informasi Sistem</p>
                        <p className="text-[11px] text-green-700/80 font-medium leading-relaxed">
                            Di sini Anda dapat menentukan syarat dokumen (berupa PDF) dan mengatur apakah surat tersebut menggunakan template sistem (otomatis) 
                            atau akan diproses manual menggunakan Microsoft Word. Gunakan <span className="font-black italic text-green-700">Custom Fields</span> untuk menambah pertanyaan khusus pada setiap jenis surat.
                        </p>
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
                                    <div className={type.is_active ? "p-3 bg-green-50 text-green-600 rounded-2xl" : "p-3 bg-gray-50 text-gray-400 rounded-2xl"}>
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
                                    <h3 className="text-lg font-black text-gray-900 tracking-tight uppercase italic leading-tight group-hover:text-green-700 transition-colors">{type.nama}</h3>
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

                            <div className={type.is_active ? "bg-green-500 h-1" : "bg-gray-200 h-1"}></div>
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

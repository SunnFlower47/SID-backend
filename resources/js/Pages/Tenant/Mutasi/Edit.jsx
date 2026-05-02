import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import MutasiFormManager from '@/Components/Mutasi/MutasiFormManager';
import { ArrowLeft, ShieldCheck, History, Eye } from 'lucide-react';

export default function Edit({ auth, mutasi, penduduks, masterRwOptions, wilayahTree }) {
    const handleBack = (e) => {
        e.preventDefault();
        window.history.back();
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Edit Mutasi - ${mutasi.penduduk?.nama}`}>
            <Head title={`Edit Mutasi - ${mutasi.penduduk?.nama}`} />

            <div className="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-20">
                
                {/* 1. CONSISTENT PREMIUM HEADER */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <History className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Edit Data Mutasi</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 flex items-center gap-2">
                                    <ShieldCheck className="w-3 h-3 text-yellow-300" />
                                    Penyuntingan Log Aktivitas Warga
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <button 
                                onClick={handleBack}
                                className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all active:scale-95 uppercase tracking-widest group"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" />
                                BATALKAN
                            </button>
                            <Link 
                                href={route('mutasi.data.show', mutasi.id)}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest"
                            >
                                <Eye className="w-4 h-4 mr-2" />
                                LIHAT DETAIL
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Form Section */}
                <div className="max-w-7xl mx-auto">
                    <div className="bg-white overflow-hidden shadow-xl sm:rounded-[40px] border border-gray-100 animate-in slide-in-from-bottom-4 duration-700">
                        <div className="p-1 border-b border-gray-50 bg-gradient-to-r from-gray-50 to-white">
                             {/* Form Manager Container */}
                             <div className="p-4 md:p-8">
                                <MutasiFormManager 
                                    mutasi={mutasi}
                                    penduduks={penduduks}
                                    masterRwOptions={masterRwOptions}
                                    wilayahTree={wilayahTree} 
                                />
                             </div>
                        </div>
                    </div>
                    
                    <div className="mt-8 p-6 bg-blue-50 rounded-[32px] border border-blue-100 flex items-start gap-4">
                        <div className="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-blue-200 shadow-sm shrink-0">
                            <ShieldCheck className="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <p className="text-xs font-black text-blue-900 uppercase tracking-tight italic">Catatan Keamanan Data</p>
                            <p className="text-[10px] font-bold text-blue-700 uppercase tracking-widest mt-1 leading-relaxed">
                                Setiap perubahan pada log mutasi akan berdampak langsung pada status kependudukan warga terkait. Pastikan data yang dimasukkan telah diverifikasi dengan dokumen fisik yang sah.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import MutasiFormManager from '@/Components/Mutasi/MutasiFormManager';
import { ShieldCheck, History, Eye } from 'lucide-react';
import { PageHeader } from '@/Components/Shared';

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
                <PageHeader 
                    title="Edit Data Mutasi"
                    subtitle="Penyuntingan Log Aktivitas Warga"
                    icon={History}
                    backAction={handleBack}
                    actions={[
                        {
                            label: 'LIHAT DETAIL',
                            icon: Eye,
                            href: route('mutasi.data.show', mutasi.id),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Form Section */}
                <div className="w-full">
                    <div className="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 animate-in slide-in-from-bottom-4 duration-700">
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
                    
                    <div className="mt-8 p-6 bg-blue-50 rounded-3xl border border-blue-100 flex items-start gap-4">
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

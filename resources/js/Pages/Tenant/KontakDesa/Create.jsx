import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import KontakDesaForm from '@/Components/KontakDesa/KontakDesaForm';
import { Phone, ArrowLeft, PlusCircle } from 'lucide-react';

export default function Create({ auth, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Kontak Desa">
            <Head title="Tambah Kontak Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <PlusCircle className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Tambah Kontak</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Pendaftaran Informasi Kontak & Instansi Desa</p>
                            </div>
                        </div>
                        <Link 
                            href={route('kontak-desa.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg"
                        >
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                            KEMBALI
                        </Link>
                    </div>
                </div>

                <KontakDesaForm jenisOptions={jenisOptions} wilayah={wilayah} />
            </div>
        </AuthenticatedLayout>
    );
}

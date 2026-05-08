import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StrukturDesaForm from '@/Components/StrukturDesa/StrukturDesaForm';
import { Edit2, Users, ArrowLeft } from 'lucide-react';

export default function Edit({ auth, strukturDesa, kategoriOptions, masterRwOptions }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Edit Perangkat Desa">
            <Head title={`Edit ${strukturDesa.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <Link 
                                href={route('struktur-desa.index')}
                                className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20 shadow-inner hover:bg-white/30 transition-all"
                            >
                                <ArrowLeft className="w-5 h-5 text-white" />
                            </Link>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Edit Perangkat</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Perbarui Data: {strukturDesa.nama}</p>
                            </div>
                        </div>
                        <div className="flex items-center space-x-3">
                            <div className="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20 shadow-inner">
                                <Edit2 className="w-6 h-6 text-yellow-300" />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Form Section */}
                <StrukturDesaForm 
                    initialData={strukturDesa}
                    kategoriOptions={kategoriOptions}
                    masterRwOptions={masterRwOptions}
                    isEdit={true}
                />
            </div>
        </AuthenticatedLayout>
    );
}

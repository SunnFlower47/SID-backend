import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BantuanSosialForm from './Form';
import { HandHeart, ArrowLeft } from 'lucide-react';

export default function Edit({ auth, bantuanSosial }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Edit Program Bantuan Sosial">
            <Head title={`Edit: ${bantuanSosial.nama_program}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <HandHeart className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">
                                    Edit Program
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    {bantuanSosial.nama_program}
                                </p>
                            </div>
                        </div>
                        <Link
                            href={route('bantuan-sosial.show', bantuanSosial.id)}
                            className="flex items-center px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                        >
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                            KEMBALI
                        </Link>
                    </div>
                </div>

                {/* Form */}
                <BantuanSosialForm mode="edit" bantuanSosial={bantuanSosial} />
            </div>
        </AuthenticatedLayout>
    );
}

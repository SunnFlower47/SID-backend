import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BeritaForm from '@/Components/Berita/BeritaForm';
import { PageHeader } from '@/Components/Shared';
import { Newspaper, ArrowLeft, Edit3 } from 'lucide-react';

export default function Edit({ auth, berita }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Edit: ${berita.judul}`}>
            <Head title={`Edit Konten: ${berita.judul} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left text-left">
                {/* Header */}
                <PageHeader
                    icon={Edit3}
                    title={berita.judul}
                    subtitle="Perbarui Detail Informasi & Publikasi"
                    actions={
                        <Link 
                            href={route('berita.index')}
                            className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg text-left text-left"
                        >
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                            KEMBALI
                        </Link>
                    }
                />

                <BeritaForm berita={berita} isEdit={true} />
            </div>
        </AuthenticatedLayout>
    );
}

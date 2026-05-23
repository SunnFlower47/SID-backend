import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BeritaForm from '@/Components/Berita/BeritaForm';
import { PageHeader } from '@/Components/Shared';
import { Newspaper, ArrowLeft, PlusCircle } from 'lucide-react';

export default function Create({ auth }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Berita">
            <Head title="Tambah Berita Baru - Admin Panel" />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left text-left">
                {/* Header */}
                <PageHeader
                    icon={PlusCircle}
                    title="Terbitkan Konten"
                    subtitle="Buat Berita, Pengumuman, atau Agenda Desa Baru"
                    actions={[
                        { label: 'Kembali', icon: ArrowLeft, href: route('berita.index'), variant: 'ghost' }
                    ]}
                />

                <BeritaForm />
            </div>
        </AuthenticatedLayout>
    );
}

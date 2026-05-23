import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import KontakDesaForm from '@/Components/KontakDesa/KontakDesaForm';
import { Phone, ArrowLeft, PlusCircle } from 'lucide-react';

export default function Create({ auth, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Kontak Desa">
            <Head title="Tambah Kontak Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader 
                    title="Tambah Kontak"
                    subtitle="Pendaftaran Informasi Kontak & Instansi Desa"
                    icon={PlusCircle}
                    backLink={route('kontak-desa.index')}
                    backLabel="KEMBALI"
                />

                <KontakDesaForm jenisOptions={jenisOptions} wilayah={wilayah} />
            </div>
        </AuthenticatedLayout>
    );
}

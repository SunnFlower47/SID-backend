import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import KontakDesaForm from '@/Components/KontakDesa/KontakDesaForm';
import { Phone, ArrowLeft, Edit3 } from 'lucide-react';

export default function Edit({ auth, kontak, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Edit Kontak: ${kontak.nama}`}>
            <Head title={`Edit Kontak: ${kontak.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader 
                    title="Edit Kontak"
                    subtitle={`Perbarui Data: ${kontak.nama}`}
                    icon={Edit3}
                    backLink={route('kontak-desa.index')}
                    backLabel="KEMBALI"
                />

                <KontakDesaForm kontak={kontak} jenisOptions={jenisOptions} wilayah={wilayah} isEdit={true} />
            </div>
        </AuthenticatedLayout>
    );
}

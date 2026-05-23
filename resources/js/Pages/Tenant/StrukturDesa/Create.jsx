import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StrukturDesaForm from '@/Components/StrukturDesa/StrukturDesaForm';
import { Plus } from 'lucide-react';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Create({ auth, kategoriOptions, masterRwOptions }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Perangkat Desa">
            <Head title="Tambah Perangkat Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                
                {/* Header */}
                <PageHeader 
                    title="Tambah Perangkat"
                    subtitle="Input Data Aparatur Desa Baru"
                    icon={Plus}
                    backHref={route('struktur-desa.index')}
                />

                {/* Form Section */}
                <StrukturDesaForm 
                    kategoriOptions={kategoriOptions}
                    masterRwOptions={masterRwOptions}
                />
            </div>
        </AuthenticatedLayout>
    );
}

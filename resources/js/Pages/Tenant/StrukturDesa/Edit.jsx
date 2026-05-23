import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StrukturDesaForm from '@/Components/StrukturDesa/StrukturDesaForm';
import { Edit2 } from 'lucide-react';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Edit({ auth, strukturDesa, kategoriOptions, masterRwOptions }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Edit Perangkat Desa">
            <Head title={`Edit ${strukturDesa.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                
                {/* Header */}
                <PageHeader 
                    title="Edit Perangkat"
                    subtitle={`Perbarui Data: ${strukturDesa.nama}`}
                    icon={Edit2}
                    backHref={route('struktur-desa.index')}
                />

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

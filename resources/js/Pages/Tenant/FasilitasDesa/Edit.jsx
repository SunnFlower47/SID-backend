import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FasilitasDesaForm from '@/Components/FasilitasDesa/FasilitasDesaForm';
import { PageHeader } from '@/Components/Shared';
import { Edit3 } from 'lucide-react';

export default function Edit({ auth, fasilitas, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Edit Fasilitas: ${fasilitas.nama}`}>
            <Head title={`Edit Fasilitas: ${fasilitas.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={Edit3}
                    title="Edit Fasilitas"
                    subtitle={`Perbarui Data: ${fasilitas.nama}`}
                    backHref={route('fasilitas-desa.index')}
                />

                <FasilitasDesaForm fasilitas={fasilitas} jenisOptions={jenisOptions} wilayah={wilayah} isEdit={true} />
            </div>
        </AuthenticatedLayout>
    );
}

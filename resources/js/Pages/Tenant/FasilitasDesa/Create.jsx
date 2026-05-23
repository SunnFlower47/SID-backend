import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FasilitasDesaForm from '@/Components/FasilitasDesa/FasilitasDesaForm';
import { PageHeader } from '@/Components/Shared';
import { PlusCircle } from 'lucide-react';

export default function Create({ auth, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Fasilitas Desa">
            <Head title="Tambah Fasilitas Desa - Admin Panel" />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={PlusCircle}
                    title="Tambah Fasilitas"
                    subtitle="Pendaftaran Sarana & Prasarana Baru Desa"
                    backHref={route('fasilitas-desa.index')}
                />

                <FasilitasDesaForm jenisOptions={jenisOptions} wilayah={wilayah} />
            </div>
        </AuthenticatedLayout>
    );
}

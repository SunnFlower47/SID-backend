import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PenerimaForm from './Form';
import { Users } from 'lucide-react';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Create({ auth, bantuanSosial }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Penerima Bantuan">
            <Head title={`Tambah Penerima: ${bantuanSosial.nama_program}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader 
                    title="Tambah Penerima"
                    subtitle={bantuanSosial.nama_program}
                    icon={Users}
                    backHref={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                />

                {/* Form */}
                <PenerimaForm mode="create" bantuanSosial={bantuanSosial} />
            </div>
        </AuthenticatedLayout>
    );
}

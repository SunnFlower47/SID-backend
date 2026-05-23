import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PenerimaForm from './Form';
import { Users } from 'lucide-react';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Edit({ auth, bantuanSosial, penerima }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Edit Penerima Bantuan">
            <Head title={`Edit Penerima: ${penerima.penduduk?.nama}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader 
                    title="Edit Penerima"
                    subtitle={`${penerima.penduduk?.nama} — ${bantuanSosial.nama_program}`}
                    icon={Users}
                    backHref={route('bantuan-sosial.penerima.show', [bantuanSosial.id, penerima.id])}
                />

                {/* Form */}
                <PenerimaForm mode="edit" bantuanSosial={bantuanSosial} penerima={penerima} />
            </div>
        </AuthenticatedLayout>
    );
}

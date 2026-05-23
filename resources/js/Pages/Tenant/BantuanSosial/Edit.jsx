import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BantuanSosialForm from './Form';
import { HandHeart } from 'lucide-react';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Edit({ auth, bantuanSosial }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Edit Program Bantuan Sosial">
            <Head title={`Edit: ${bantuanSosial.nama_program}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader 
                    title="Edit Program"
                    subtitle={bantuanSosial.nama_program}
                    icon={HandHeart}
                    backHref={route('bantuan-sosial.show', bantuanSosial.id)}
                />

                {/* Form */}
                <BantuanSosialForm mode="edit" bantuanSosial={bantuanSosial} />
            </div>
        </AuthenticatedLayout>
    );
}

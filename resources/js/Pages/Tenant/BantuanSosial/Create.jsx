import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BantuanSosialForm from './Form';
import { HandHeart } from 'lucide-react';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Create({ auth }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Program Bantuan Sosial">
            <Head title="Tambah Program Bantuan Sosial" />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader 
                    title="Tambah Program"
                    subtitle="Buat program bantuan sosial baru"
                    icon={HandHeart}
                    backHref={route('bantuan-sosial.index')}
                />

                {/* Form */}
                <BantuanSosialForm mode="create" />
            </div>
        </AuthenticatedLayout>
    );
}

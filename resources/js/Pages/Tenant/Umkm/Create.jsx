import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UmkmForm from '@/Components/Umkm/UmkmForm';
import { PageHeader } from '@/Components/Shared';
import { Store, ArrowLeft, PlusCircle } from 'lucide-react';

export default function Create({ auth, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Data UMKM">
            <Head title="Tambah Data UMKM - Admin Panel" />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={PlusCircle}
                    title="Tambah UMKM"
                    subtitle="Pendaftaran Potensi Ekonomi Desa Baru"
                    backHref={route('umkm.index')}
                />

                <UmkmForm jenisOptions={jenisOptions} wilayah={wilayah} />
            </div>
        </AuthenticatedLayout>
    );
}

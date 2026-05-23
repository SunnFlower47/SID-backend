import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UmkmForm from '@/Components/Umkm/UmkmForm';
import { PageHeader } from '@/Components/Shared';
import { Store, ArrowLeft, Edit3 } from 'lucide-react';

export default function Edit({ auth, umkm, jenisOptions, wilayah }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Edit UMKM: ${umkm.nama_usaha}`}>
            <Head title={`Edit UMKM: ${umkm.nama_usaha} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={Edit3}
                    title="Edit Data UMKM"
                    subtitle={`Perbarui Informasi: ${umkm.nama_usaha}`}
                    backHref={route('umkm.index')}
                />

                <UmkmForm umkm={umkm} jenisOptions={jenisOptions} wilayah={wilayah} isEdit={true} />
            </div>
        </AuthenticatedLayout>
    );
}

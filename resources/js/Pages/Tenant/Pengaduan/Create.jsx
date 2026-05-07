import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PengaduanForm from './Form';

export default function Create({ auth }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Pengaduan Manual">
            <Head title="Tambah Pengaduan" />
            <PengaduanForm isEdit={false} />
        </AuthenticatedLayout>
    );
}

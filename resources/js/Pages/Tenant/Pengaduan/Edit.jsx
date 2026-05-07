import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PengaduanForm from './Form';

export default function Edit({ auth, pengaduan }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Tanggapi Pengaduan">
            <Head title={`Tanggapi: ${pengaduan.judul}`} />
            <PengaduanForm isEdit={true} pengaduan={pengaduan} />
        </AuthenticatedLayout>
    );
}

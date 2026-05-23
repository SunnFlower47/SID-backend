import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import MutasiFormManager from '@/Components/Mutasi/MutasiFormManager';
import { Plus, History } from 'lucide-react';
import { PageHeader } from '@/Components/Shared';

export default function Create({ auth, wilayahTree }) {
    const handleBack = (e) => {
        e.preventDefault();
        window.history.back();
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Input Mutasi Baru">
            <Head title="Input Mutasi Baru" />

            <div className="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-20">
                
                {/* 1. CONSISTENT PREMIUM HEADER */}
                <PageHeader 
                    title="Input Mutasi"
                    subtitle="Pencatatan Peristiwa Kependudukan"
                    icon={Plus}
                    backAction={handleBack}
                    actions={[
                        {
                            label: 'RIWAYAT',
                            icon: History,
                            href: route('mutasi.data.index'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Form Section */}
                <div className="w-full">
                    <div className="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 animate-in slide-in-from-bottom-4 duration-700">
                        <div className="p-4 md:p-10">
                            <MutasiFormManager wilayahTree={wilayahTree} />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

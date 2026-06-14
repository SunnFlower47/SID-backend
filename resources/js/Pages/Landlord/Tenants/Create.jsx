import { Head, Link, useForm } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Building2, Plus, User, Key, ChevronLeft } from 'lucide-react';

export default function Create({ baseDomain = 'sistem-desa-cibatu.test' }) {
    const { data, setData, post, processing, errors } = useForm({
        id: '',
        name: '',
        domain: '',
        operator_name: '',
        operator_email: '',
        operator_password: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('tenants.store'));
    };

    // Helper to auto-fill domain based on slug
    const handleIdChange = (value) => {
        const cleanedValue = value.toLowerCase().replace(/[^a-z0-9-_]/g, '');
        setData(data => ({
            ...data,
            id: cleanedValue,
            domain: cleanedValue ? `${cleanedValue}.${baseDomain}` : ''
        }));
    };

    return (
        <LandlordLayout>
            <Head title="Registrasi Desa Baru" />

            <div className="space-y-8">
                {/* Header */}
                <PageHeader 
                    icon={Building2}
                    title="Registrasi Desa Baru"
                    subtitle="Daftarkan desa (tenant) baru ke dalam sistem dan buat databasenya."
                    backHref={route('tenants.index')}
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                <form onSubmit={submit} className="space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Section: Info Desa */}
                        <FormCard 
                            icon={Building2}
                            title="Informasi Desa"
                        >
                            <div className="space-y-4">
                                <FormField.Input 
                                    label="Nama Desa"
                                    placeholder="Contoh: Desa Wanayasa"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    error={errors.name}
                                    required
                                />

                                <FormField.Input 
                                    label="ID / Slug (Untuk database)"
                                    placeholder="Contoh: wanayasa"
                                    value={data.id}
                                    onChange={e => handleIdChange(e.target.value)}
                                    error={errors.id}
                                    required
                                />
                                <p className="text-[10px] text-gray-400 font-bold -mt-2 ml-1 uppercase tracking-tight">
                                    Akan menjadi nama database: db_tenant_xxx
                                </p>

                                <FormField.Input 
                                    label="Domain Utama (Web Desa)"
                                    placeholder={`Contoh: wanayasa.${baseDomain}`}
                                    value={data.domain}
                                    onChange={e => setData('domain', e.target.value)}
                                    error={errors.domain}
                                    required
                                    className="bg-gray-100/50"
                                    readOnly
                                />
                            </div>
                        </FormCard>

                        {/* Section: Akun Operator */}
                        <FormCard 
                            icon={User}
                            title="Akun Operator Desa"
                        >
                            <div className="space-y-4">
                                <FormField.Input 
                                    label="Nama Lengkap Operator"
                                    placeholder="Nama Lengkap Admin Desa"
                                    value={data.operator_name}
                                    onChange={e => setData('operator_name', e.target.value)}
                                    error={errors.operator_name}
                                    required
                                />

                                <FormField.Input 
                                    label="Email Operator"
                                    type="email"
                                    placeholder="operator@wanayasa.desa.id"
                                    value={data.operator_email}
                                    onChange={e => setData('operator_email', e.target.value)}
                                    error={errors.operator_email}
                                    required
                                />

                                <FormField.Input 
                                    label="Password Sementara"
                                    type="text"
                                    placeholder="Minimal 8 Karakter"
                                    value={data.operator_password}
                                    onChange={e => setData('operator_password', e.target.value)}
                                    error={errors.operator_password}
                                    required
                                />
                            </div>
                        </FormCard>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <Link 
                            href={route('tenants.index')} 
                            className="inline-flex justify-center py-3 px-6 rounded-2xl border border-gray-200 text-sm font-bold text-gray-500 hover:bg-gray-100 transition-colors"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex justify-center py-3 px-8 border border-transparent shadow-lg shadow-indigo-600/20 text-sm font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all disabled:opacity-50"
                        >
                            {processing ? 'Memproses...' : 'Buat Tenant & Database'}
                        </button>
                    </div>
                </form>
            </div>
        </LandlordLayout>
    );
}

import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Building2, Plus, User, Key, ChevronLeft, Loader2, Check, Terminal } from 'lucide-react';

function OnboardingLoader({ tenantId }) {
    const [visibleSteps, setVisibleSteps] = useState([]);
    const [currentStep, setCurrentStep] = useState(0);
    const logEndRef = useRef(null);

    const steps = [
        { text: 'Memvalidasi formulir & data wilayah desa...', duration: 800 },
        { text: `Mendaftarkan desa baru (${tenantId}) ke database central...`, duration: 800 },
        { text: 'Mengalokasikan subdomain & DNS routing...', duration: 1000 },
        { text: `Membuat basis data terisolasi db_tenant_${tenantId}...`, duration: 1200 },
        { text: 'Menjalankan migrasi skema tabel desa (ini memerlukan waktu)...', duration: 3500 },
        { text: 'Menyisipkan data awal (seeder) & peran default desa...', duration: 1200 },
        { text: 'Menginisialisasi direktori penyimpanan berkas (storage)...', duration: 1000 },
        { text: 'Menyiapkan akun administrator default...', duration: 800 },
        { text: 'Penyelesaian & sinkronisasi alokasi kuota...', duration: 800 },
    ];

    useEffect(() => {
        if (currentStep < steps.length) {
            const timer = setTimeout(() => {
                setVisibleSteps(prev => [
                    ...prev.map(s => ({ ...s, status: 'success' })),
                    { text: steps[currentStep].text, status: 'loading' }
                ]);
                setCurrentStep(curr => curr + 1);
            }, currentStep === 0 ? 0 : steps[currentStep - 1].duration);

            return () => clearTimeout(timer);
        } else {
            // Akhiri dengan status sukses semua
            const timer = setTimeout(() => {
                setVisibleSteps(prev => prev.map(s => ({ ...s, status: 'success' })));
            }, 500);
            return () => clearTimeout(timer);
        }
    }, [currentStep]);

    useEffect(() => {
        logEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [visibleSteps]);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md transition-all duration-300">
            <style dangerouslySetInnerHTML={{ __html: `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(4px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-fadeIn {
                    animation: fadeIn 0.2s ease-out forwards;
                }
            ` }} />
            
            {/* Background glowing effects */}
            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none" />
            
            <div className="bg-slate-900 border border-slate-800 p-8 rounded-3xl max-w-lg w-full shadow-2xl relative overflow-hidden flex flex-col items-center">
                {/* Glowing border top */}
                <div className="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-500 animate-pulse" />

                {/* Animated Logo / Spinner */}
                <div className="relative flex items-center justify-center w-24 h-24 mb-6">
                    <div className="absolute inset-0 border-4 border-indigo-500/10 rounded-full" />
                    <div className="absolute inset-0 border-4 border-t-indigo-500 border-r-purple-500 rounded-full animate-spin" />
                    <div className="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center border border-slate-700 shadow-inner">
                        <Building2 className="w-6 h-6 text-indigo-400 animate-pulse" />
                    </div>
                </div>

                <div className="text-center space-y-2">
                    <h3 className="text-xl font-black text-white tracking-tight">Mempersiapkan Desa Baru</h3>
                    <p className="text-slate-400 text-xs max-w-sm leading-relaxed">
                        Sistem sedang membuat lingkungan basis data terisolasi untuk desa baru Anda. Mohon tidak menutup halaman ini.
                    </p>
                </div>

                {/* Console Log Window */}
                <div className="w-full mt-6 bg-slate-950/90 border border-slate-800 rounded-2xl overflow-hidden font-mono text-[11px] shadow-inner">
                    {/* Header Terminal */}
                    <div className="bg-slate-900/60 border-b border-slate-800/80 px-4 py-2 flex items-center justify-between">
                        <div className="flex items-center gap-1.5">
                            <span className="w-2 h-2 rounded-full bg-red-500/40 border border-red-500/50" />
                            <span className="w-2 h-2 rounded-full bg-yellow-500/40 border border-yellow-500/50" />
                            <span className="w-2 h-2 rounded-full bg-green-500/40 border border-green-500/50" />
                        </div>
                        <span className="text-[9px] uppercase tracking-wider text-slate-500 font-extrabold flex items-center gap-1">
                            <Terminal className="w-3.5 h-3.5" /> ONBOARDING LOGS
                        </span>
                    </div>

                    {/* Log Terminal Screen */}
                    <div className="p-4 h-52 overflow-y-auto space-y-2 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
                        {visibleSteps.map((step, idx) => (
                            <div key={idx} className="flex items-start gap-2.5 animate-fadeIn">
                                {step.status === 'loading' ? (
                                    <span className="text-indigo-400 font-bold shrink-0 animate-pulse">[ RUN ]</span>
                                ) : (
                                    <span className="text-emerald-500 font-bold shrink-0">[  OK  ]</span>
                                )}
                                <span className={step.status === 'loading' ? 'text-slate-100 font-semibold animate-pulse' : 'text-slate-400'}>
                                    {step.text}
                                </span>
                            </div>
                        ))}
                        <div ref={logEndRef} />
                    </div>
                </div>
            </div>
        </div>
    );
}

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

            {/* Premium Onboarding Loader Overlay */}
            {processing && <OnboardingLoader tenantId={data.id || 'desa'} />}
        </LandlordLayout>
    );
}

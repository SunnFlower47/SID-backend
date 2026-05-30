import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { MessageSquare, Save, User, MapPin, Star } from 'lucide-react';

export default function Create({ auth }) {
    const { data, setData, post, processing, errors } = useForm({
        nama: '', email: '', telepon: '',
        testimoni: '', rating: 5, kategori: 'umum', is_anonymous: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('testimoni.store'));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Testimoni">
            <Head title="Tambah Testimoni Warga" />

            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-10 text-left">
                <PageHeader
                    icon={MessageSquare}
                    title="Tambah Testimoni"
                    subtitle="Input data testimoni baru"
                    backUrl={route('testimoni.index')}
                />

                <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-6 text-left">
                    <div className="lg:col-span-2 space-y-6 text-left">
                        <FormCard title="Detail Testimoni" icon={MessageSquare}>
                            <div className="space-y-6 text-left">
                                <FormField.Textarea
                                    label="Isi Testimoni"
                                    value={data.testimoni}
                                    onChange={e => setData('testimoni', e.target.value)}
                                    placeholder="Ketik testimoni di sini..."
                                    error={errors.testimoni}
                                    className="min-h-[200px]"
                                />
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                    <div className="text-left">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block text-left">Rating Kepuasan</label>
                                        <div className="flex items-center gap-2 p-4 bg-gray-50 rounded-2xl shadow-inner text-left">
                                            {[1, 2, 3, 4, 5].map(s => (
                                                <button key={s} type="button" onClick={() => setData('rating', s)} className="focus:outline-none transform hover:scale-110 transition-transform text-left">
                                                    <Star className={`w-8 h-8 ${data.rating >= s ? 'fill-orange-400 text-orange-400' : 'text-gray-200'}`} />
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                    <FormField.Select
                                        label="Kategori"
                                        value={data.kategori}
                                        onChange={e => setData('kategori', e.target.value)}
                                        options={[
                                            { value: 'umum', label: 'Umum' },
                                            { value: 'pelayanan', label: 'Pelayanan' },
                                            { value: 'pembangunan', label: 'Pembangunan' }
                                        ]}
                                    />
                                </div>
                            </div>
                        </FormCard>
                        
                        <div className="flex justify-end text-left">
                            <button type="submit" disabled={processing} className="px-12 py-5 bg-gray-900 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3 text-left">
                                <Save className="w-5 h-5 text-left" /> SIMPAN DATA TESTIMONI
                            </button>
                        </div>
                    </div>

                    <div className="space-y-6 text-left">
                        <FormCard title="Informasi Pengirim" icon={User} compact>
                            <div className="space-y-4 text-left">
                                <FormField.Input
                                    placeholder="Nama Lengkap"
                                    value={data.nama}
                                    onChange={e => setData('nama', e.target.value)}
                                    error={errors.nama}
                                />
                                <FormField.Input
                                    type="email"
                                    placeholder="Email (Opsional)"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    error={errors.email}
                                />
                                <FormField.Input
                                    placeholder="Telepon (Opsional)"
                                    value={data.telepon}
                                    onChange={e => setData('telepon', e.target.value)}
                                    error={errors.telepon}
                                />
                                <div className="flex items-center gap-2 px-2 text-left mt-2">
                                    <input 
                                        type="checkbox" 
                                        id="anon" 
                                        checked={data.is_anonymous} 
                                        onChange={e => setData('is_anonymous', e.target.checked)} 
                                        className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                    />
                                    <label htmlFor="anon" className="text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer text-left">Anonim</label>
                                </div>
                            </div>
                        </FormCard>


                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
